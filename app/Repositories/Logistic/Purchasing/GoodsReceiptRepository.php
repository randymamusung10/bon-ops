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

    public function delete(GoodsReceipt $receipt)
    {
        $receipt->items()->delete();
        $receipt->delete();
    }
}
