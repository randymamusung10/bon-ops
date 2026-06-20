<?php

namespace App\Services\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockWasteItem;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use App\Models\Logistic\Master\Product\Product;
use App\Repositories\Logistic\Inventory\StockWasteRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StockWasteService
{
    protected $repository;

    public function __construct(StockWasteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $docNo = 'SW-' . date('YmdHis');

            $waste = $this->repository->create([
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
                $product = Product::findOrFail($item['product_id']);
                $cost = $product->cost ?? 0;

                StockWasteItem::create([
                    'stock_waste_id' => $waste->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'reason' => $item['reason'] ?? null,
                    'cost' => $cost,
                ]);
            }

            DB::commit();
            return $waste;
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
            $waste = $this->repository->findByUuid($tenantId, $uuid);

            if ($waste->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat diedit.");
            }

            $this->repository->update($waste, [
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Hapus item lama
            $waste->items()->delete();

            // Masukkan item baru
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $cost = $product->cost ?? 0;

                StockWasteItem::create([
                    'stock_waste_id' => $waste->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'reason' => $item['reason'] ?? null,
                    'cost' => $cost,
                ]);
            }

            DB::commit();
            return $waste;
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
            
            $waste = $this->repository->findByUuid($tenantId, $uuid);

            if ($waste->status !== 'draft') {
                throw new Exception("Hanya dokumen draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($waste, 'submitted', [
                'submitted_by' => Auth::id(),
                'submitted_at' => now()
            ]);

            DB::commit();
            return $waste;
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
            
            $waste = $this->repository->findByUuid($tenantId, $uuid);

            if ($waste->status !== 'submitted') {
                throw new Exception("Hanya dokumen submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($waste, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $waste;
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

            $waste = $this->repository->findByUuid($tenantId, $uuid);

            if ($waste->status !== 'approved') {
                throw new Exception("Hanya dokumen approved yang dapat diposting.");
            }

            foreach ($waste->items as $item) {
                // Lock row for update to prevent race conditions
                $balance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $waste->warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$balance) {
                    $balance = InventoryBalance::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $waste->branch_id,
                        'warehouse_id' => $waste->warehouse_id,
                        'product_id' => $item->product_id,
                        'qty' => 0
                    ]);
                }

                $newBalance = $balance->qty - $item->qty;

                // Create Movement
                InventoryMovement::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $waste->branch_id,
                    'warehouse_id' => $waste->warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'stock_waste',
                    'reference_id' => $waste->id,
                    'reference_number' => $waste->document_number,
                    'date' => $waste->date,
                    'qty_in' => 0,
                    'qty_out' => $item->qty,
                    'balance_after' => $newBalance,
                    'notes' => $item->reason,
                ]);

                // Set final balance
                $balance->update(['qty' => $newBalance]);
            }

            $this->repository->updateStatus($waste, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
            ]);

            DB::commit();
            return $waste;
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

            $waste = $this->repository->findByUuid($tenantId, $uuid);

            if ($waste->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
            }

            $this->repository->delete($waste);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
