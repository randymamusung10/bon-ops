<?php

namespace App\Repositories\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\SupplierPayment;

class SupplierPaymentRepository
{
    public function datatable($tenantId)
    {
        return SupplierPayment::with(['supplier', 'supplierInvoice', 'creator'])
            ->where('tenant_id', $tenantId)
            ->select('supplier_payments.*');
    }

    public function findByUuid($tenantId, $uuid)
    {
        return SupplierPayment::with([
            'supplier',
            'supplierInvoice',
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
        return SupplierPayment::create($data);
    }

    public function updateStatus(SupplierPayment $payment, string $status, array $additionalData = [])
    {
        $payment->status = $status;
        foreach ($additionalData as $key => $value) {
            $payment->{$key} = $value;
        }
        $payment->save();
        return $payment;
    }

    public function delete(SupplierPayment $payment)
    {
        $payment->delete();
    }
}
