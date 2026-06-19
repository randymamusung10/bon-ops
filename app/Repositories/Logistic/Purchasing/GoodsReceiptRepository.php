<?php

namespace App\Repositories\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\GoodsReceipt;
use Illuminate\Support\Str;

class GoodsReceiptRepository
{
    public function datatable($tenantId)
    {
        return GoodsReceipt::with(['warehouse', 'supplier', 'creator'])
            ->where('tenant_id', $tenantId)
            ->select('goods_receipts.*');
    }

    public function findByUuid($tenantId, $uuid)
    {
        return GoodsReceipt::with([
            'items.product.unit', 
            'items.purchaseOrderItem',
            'warehouse.branch', 
            'supplier', 
            'creator', 
            'approver', 
            'poster',
            'purchaseOrder'
        ])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return GoodsReceipt::create($data);
    }

    public function updateStatus(GoodsReceipt $receipt, string $status, array $additionalData = [])
    {
        $receipt->status = $status;
        foreach ($additionalData as $key => $value) {
            $receipt->{$key} = $value;
        }
        $receipt->save();
        return $receipt;
    }

    public function updateDraft($uuid, array $data, array $items, $tenantId, $userId)
    {
        $receipt = $this->findByUuid($tenantId, $uuid);

        if ($receipt->status !== 'draft') {
            throw new \Exception('Hanya dokumen dengan status Draft yang dapat diedit.');
        }

        $receipt->update([
            'branch_id' => $data['branch_id'],
            'warehouse_id' => $data['warehouse_id'],
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'supplier_id' => $data['supplier_id'] ?? null,
            'date' => $data['date'],
            'notes' => $data['notes'] ?? null,
        ]);

        $receipt->items()->delete();

        foreach ($items as $item) {
            $receipt->items()->create([
                'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                'product_id' => $item['product_id'],
                'unit_id' => $item['unit_id'],
                'ordered_qty' => $item['ordered_qty'] ?? 0,
                'received_qty' => $item['received_qty'],
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return $receipt;
    }

    public function delete(GoodsReceipt $receipt)
    {
        $receipt->items()->delete();
        $receipt->delete();
    }
}
