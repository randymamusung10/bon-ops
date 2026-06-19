<?php

namespace App\Services\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockAdjustmentItem;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use App\Repositories\Logistic\Inventory\StockAdjustmentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StockAdjustmentService
{
    protected $repository;

    public function __construct(StockAdjustmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $docNo = 'SA-' . date('YmdHis');

            $adjustment = $this->repository->create([
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
                // Ideally this would be in another repository, but kept here to avoid over-engineering if no InventoryBalanceRepository exists
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

    public function update(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $adjustment = $this->repository->findByUuid($tenantId, $uuid);

            if ($adjustment->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat diedit.");
            }

            $this->repository->update($adjustment, [
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'date' => $data['date'],
                'notes' => $data['notes'],
            ]);

            // Hapus item lama
            $adjustment->items()->delete();

            // Masukkan item baru
            foreach ($data['items'] as $item) {
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

    public function submitDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;
            
            $adjustment = $this->repository->findByUuid($tenantId, $uuid);

            if ($adjustment->status !== 'draft') {
                throw new Exception("Hanya dokumen draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($adjustment, 'submitted', [
                'submitted_by' => Auth::id(),
                'submitted_at' => now()
            ]);

            DB::commit();
            return $adjustment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;
            
            $adjustment = $this->repository->findByUuid($tenantId, $uuid);

            if ($adjustment->status !== 'submitted') {
                throw new Exception("Hanya dokumen submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($adjustment, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $adjustment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function postDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $adjustment = $this->repository->findByUuid($tenantId, $uuid);

            if ($adjustment->status !== 'approved') {
                throw new Exception("Hanya dokumen approved yang dapat diposting.");
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
                    'reference_number' => $adjustment->document_number,
                    'date' => $adjustment->date,
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                    'balance_after' => $newBalance,
                    'notes' => $item->reason,
                ]);

                // Set final balance
                $balance->update(['qty' => $newBalance]);
            }

            $this->repository->updateStatus($adjustment, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
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

            $adjustment = $this->repository->findByUuid($tenantId, $uuid);

            if ($adjustment->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
            }

            $this->repository->delete($adjustment);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
