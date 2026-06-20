<?php

namespace App\Services\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockOpnameItem;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use App\Repositories\Logistic\Inventory\StockOpnameRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StockOpnameService
{
    protected $repository;

    public function __construct(StockOpnameRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $docNo = 'SO-' . date('YmdHis');

            $opname = $this->repository->create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'document_number' => $docNo,
                'date' => $data['date'],
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $balance = InventoryBalance::where('tenant_id', $tenantId)
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();
                
                $systemQty = $balance ? $balance->qty : 0;
                $actualQty = $item['actual_qty'];
                $difference = $actualQty - $systemQty;

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $item['product_id'],
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $opname;
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
            $opname = $this->repository->findByUuid($tenantId, $uuid);

            if ($opname->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat diedit.");
            }

            $this->repository->update($opname, [
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Hapus item lama
            $opname->items()->delete();

            // Masukkan item baru
            foreach ($data['items'] as $item) {
                $balance = InventoryBalance::where('tenant_id', $tenantId)
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->where('product_id', $item['product_id'])
                    ->first();
                
                $systemQty = $balance ? $balance->qty : 0;
                $actualQty = $item['actual_qty'];
                $difference = $actualQty - $systemQty;

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $item['product_id'],
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $opname;
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
            
            $opname = $this->repository->findByUuid($tenantId, $uuid);

            if ($opname->status !== 'draft') {
                throw new Exception("Hanya dokumen draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($opname, 'submitted', [
                'submitted_by' => Auth::id(),
                'submitted_at' => now()
            ]);

            DB::commit();
            return $opname;
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
            
            $opname = $this->repository->findByUuid($tenantId, $uuid);

            if ($opname->status !== 'submitted') {
                throw new Exception("Hanya dokumen submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($opname, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $opname;
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

            $opname = $this->repository->findByUuid($tenantId, $uuid);

            if ($opname->status !== 'approved') {
                throw new Exception("Hanya dokumen approved yang dapat diposting.");
            }

            foreach ($opname->items as $item) {
                // Lock row for update to prevent race conditions
                $balance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $opname->warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$balance) {
                    $balance = InventoryBalance::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $opname->branch_id,
                        'warehouse_id' => $opname->warehouse_id,
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
                    'branch_id' => $opname->branch_id,
                    'warehouse_id' => $opname->warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'stock_opname',
                    'reference_id' => $opname->id,
                    'reference_number' => $opname->document_number,
                    'date' => $opname->date,
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                    'balance_after' => $newBalance,
                    'notes' => $item->notes,
                ]);

                // Set final balance
                $balance->update(['qty' => $newBalance]);
            }

            $this->repository->updateStatus($opname, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
            ]);

            DB::commit();
            return $opname;
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

            $opname = $this->repository->findByUuid($tenantId, $uuid);

            if ($opname->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
            }

            $this->repository->delete($opname);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
