<?php

namespace App\Services\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockAdjustment;
use App\Models\Logistic\Inventory\StockAdjustmentItem;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StockAdjustmentService
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            
            // Generate simple document number
            $docNo = 'SA-' . date('YmdHis');

            $adjustment = StockAdjustment::create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'document_number' => $docNo,
                'date' => $data['date'],
                'status' => 'draft',
                'notes' => $data['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                // Get system qty
                $balance = InventoryBalance::where('tenant_id', $tenantId)
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();
                
                $systemQty = $balance ? $balance->qty : 0;
                $actualQty = $item['actual_qty'];
                $difference = $actualQty - $systemQty;

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            DB::commit();
            return $adjustment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function post(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $adjustment = StockAdjustment::with('items')->where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

            if ($adjustment->status !== 'draft') {
                throw new Exception("Dokumen sudah diposting atau dibatalkan.");
            }

            foreach ($adjustment->items as $item) {
                // Lock row for update to prevent race conditions
                $balance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$balance) {
                    $balance = InventoryBalance::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $adjustment->branch_id,
                        'warehouse_id' => $adjustment->warehouse_id,
                        'product_id' => $item->product_id,
                        'qty' => 0
                    ]);
                }

                $qtyIn = $item->difference > 0 ? $item->difference : 0;
                $qtyOut = $item->difference < 0 ? abs($item->difference) : 0;
                
                $newBalance = $balance->qty + $item->difference;

                // Create Movement
                InventoryMovement::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $adjustment->branch_id,
                    'warehouse_id' => $adjustment->warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'stock_adjustment',
                    'reference_id' => $adjustment->id,
                    'date' => $adjustment->date,
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                    'balance_after' => $newBalance,
                    'notes' => $item->reason,
                ]);

                // Set final balance
                $balance->update(['qty' => $newBalance]);
            }

            $adjustment->update([
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            DB::commit();
            return $adjustment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $adjustment = StockAdjustment::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

            if ($adjustment->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
            }

            // Hapus items (bisa menggunakan relasi atau kaskade jika diatur, namun lebih aman hapus eksplisit)
            $adjustment->items()->delete();
            $adjustment->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
