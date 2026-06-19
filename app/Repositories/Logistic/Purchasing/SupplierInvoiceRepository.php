<?php

namespace App\Repositories\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\SupplierInvoice;

class SupplierInvoiceRepository
{
    public function datatable($tenantId)
    {
        return SupplierInvoice::with(['supplier', 'goodsReceipt', 'purchaseOrder', 'creator'])
            ->where('tenant_id', $tenantId)
            ->select('supplier_invoices.*');
    }

    public function findByUuid($tenantId, $uuid)
    {
        return SupplierInvoice::with([
            'items.product.unit', 
            'supplier', 
            'goodsReceipt',
            'purchaseOrder',
            'creator', 
            'approver', 
            'poster'
        ])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return SupplierInvoice::create($data);
    }

    public function updateStatus(SupplierInvoice $invoice, string $status, array $additionalData = [])
    {
        $invoice->status = $status;
        foreach ($additionalData as $key => $value) {
            $invoice->{$key} = $value;
        }
        $invoice->save();
        return $invoice;
    }

    public function delete(SupplierInvoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->delete();
    }
}
