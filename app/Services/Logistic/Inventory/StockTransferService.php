<?php

namespace App\Services\Logistic\Inventory;

use App\Repositories\Logistic\Inventory\StockTransferRepository;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class StockTransferService
{
    protected $repository;

    public function __construct(StockTransferRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            
            // Generate simple document number
            $docNo = 'ST-' . date('YmdHis');

            $transfer = $this->repository->create([
                'tenant_id' => $tenantId,
                'source_branch_id' => $data['source_branch_id'],
                'source_warehouse_id' => $data['source_warehouse_id'],
                'destination_branch_id' => $data['destination_branch_id'],
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'document_number' => $docNo,
                'date' => $data['date'],
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $transfer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDraft(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $transfer = $this->repository->findByUuid($tenantId, $uuid);

            if ($transfer->status !== 'draft') {
                throw new Exception("Hanya dokumen berstatus draft yang bisa diedit.");
            }

            $this->repository->update($transfer, [
                'source_branch_id' => $data['source_branch_id'],
                'source_warehouse_id' => $data['source_warehouse_id'],
                'destination_branch_id' => $data['destination_branch_id'],
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Sync items
            $transfer->items()->delete();
            foreach ($data['items'] as $item) {
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $transfer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function submitDocument(string $uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transfer = $this->repository->findByUuid($tenantId, $uuid);

        if ($transfer->status !== 'draft') {
            throw new Exception("Hanya dokumen berstatus draft yang bisa diajukan.");
        }

        return $this->repository->update($transfer, [
            'status' => 'submitted'
        ]);
    }

    public function approveDocument(string $uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transfer = $this->repository->findByUuid($tenantId, $uuid);

        if ($transfer->status !== 'submitted') {
            throw new Exception("Dokumen belum diajukan (submitted).");
        }

        return $this->repository->update($transfer, [
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    public function postDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;
            $transfer = $this->repository->findByUuid($tenantId, $uuid);

            if ($transfer->status !== 'approved') {
                throw new Exception("Dokumen belum disetujui (approved).");
            }

            foreach ($transfer->items as $item) {
                // 1. DEDUCT FROM SOURCE WAREHOUSE
                $sourceBalance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $transfer->source_warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$sourceBalance || $sourceBalance->qty < $item->qty) {
                    throw new Exception("Stok untuk produk {$item->product->name} tidak mencukupi di gudang asal.");
                }

                $newSourceQty = $sourceBalance->qty - $item->qty;
                
                InventoryMovement::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $transfer->source_branch_id,
                    'warehouse_id' => $transfer->source_warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'stock_transfer_out',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->document_number,
                    'date' => $transfer->date,
                    'qty_in' => 0,
                    'qty_out' => $item->qty,
                    'balance_after' => $newSourceQty,
                    'notes' => 'Transfer Out to ' . $transfer->destinationWarehouse->name,
                ]);

                $sourceBalance->update(['qty' => $newSourceQty]);

                // 2. ADD TO DESTINATION WAREHOUSE
                $destBalance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $transfer->destination_warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$destBalance) {
                    $destBalance = InventoryBalance::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $transfer->destination_branch_id,
                        'warehouse_id' => $transfer->destination_warehouse_id,
                        'product_id' => $item->product_id,
                        'qty' => 0
                    ]);
                }

                $newDestQty = $destBalance->qty + $item->qty;

                InventoryMovement::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $transfer->destination_branch_id,
                    'warehouse_id' => $transfer->destination_warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'stock_transfer_in',
                    'reference_id' => $transfer->id,
                    'reference_number' => $transfer->document_number,
                    'date' => $transfer->date,
                    'qty_in' => $item->qty,
                    'qty_out' => 0,
                    'balance_after' => $newDestQty,
                    'notes' => 'Transfer In from ' . $transfer->sourceWarehouse->name,
                ]);

                $destBalance->update(['qty' => $newDestQty]);
            }

            $this->repository->update($transfer, [
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_at' => now(),
            ]);

            DB::commit();
            return $transfer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(string $uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transfer = $this->repository->findByUuid($tenantId, $uuid);

        if ($transfer->status !== 'draft') {
            throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
        }

        $transfer->items()->delete();
        return $this->repository->delete($transfer);
    }
}
