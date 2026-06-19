<?php

namespace App\Services\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\GoodsReceiptItem;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use App\Repositories\Logistic\Purchasing\GoodsReceiptRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class GoodsReceiptService
{
    protected $repository;

    public function __construct(GoodsReceiptRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $docNo = 'GR-' . date('YmdHis') . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);

            $receipt = $this->repository->create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'],
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'document_number' => $docNo,
                'date' => $data['date'],
                'status' => 'draft',
                'notes' => $data['notes'],
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'ordered_qty' => $item['ordered_qty'] ?? 0,
                    'received_qty' => $item['received_qty'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $receipt;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDraft($uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $userId = Auth::id();
            $items = $data['items'];

            $receipt = $this->repository->updateDraft($uuid, $data, $items, $tenantId, $userId);

            DB::commit();
            return $receipt;
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
            
            $receipt = $this->repository->findByUuid($tenantId, $uuid);

            if ($receipt->status !== 'draft') {
                throw new Exception("Hanya dokumen draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($receipt, 'submitted');

            DB::commit();
            return $receipt;
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
            
            $receipt = $this->repository->findByUuid($tenantId, $uuid);

            if ($receipt->status !== 'submitted') {
                throw new Exception("Hanya dokumen submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($receipt, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $receipt;
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

            $receipt = $this->repository->findByUuid($tenantId, $uuid);

            if ($receipt->status !== 'approved') {
                throw new Exception("Hanya dokumen approved yang dapat diposting.");
            }

            // Integrasi ke Inventory Balance & Movement
            foreach ($receipt->items as $item) {
                // Gunakan lockForUpdate untuk mencegah race condition pada Inventory
                $balance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $receipt->warehouse_id,
                    'product_id' => $item->product_id,
                ])->lockForUpdate()->first();

                if (!$balance) {
                    $balance = InventoryBalance::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $receipt->branch_id,
                        'warehouse_id' => $receipt->warehouse_id,
                        'product_id' => $item->product_id,
                        'qty' => 0
                    ]);
                }

                $newQty = $balance->qty + $item->received_qty;

                // Catat mutasi masuk
                InventoryMovement::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $receipt->branch_id,
                    'warehouse_id' => $receipt->warehouse_id,
                    'product_id' => $item->product_id,
                    'reference_type' => 'goods_receipt',
                    'reference_id' => $receipt->id,
                    'reference_number' => $receipt->document_number,
                    'date' => $receipt->date,
                    'qty_in' => $item->received_qty,
                    'qty_out' => 0,
                    'balance_after' => $newQty,
                    'notes' => 'Penerimaan Barang ' . $receipt->document_number,
                ]);

                // Update saldo akhir gudang
                $balance->update(['qty' => $newQty]);
            }

            $this->repository->updateStatus($receipt, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
            ]);

            DB::commit();
            return $receipt;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $receipt = $this->repository->findByUuid($tenantId, $uuid);

            if ($receipt->status !== 'draft') {
                throw new Exception("Hanya dokumen dengan status draft yang dapat dihapus.");
            }

            $this->repository->delete($receipt);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
