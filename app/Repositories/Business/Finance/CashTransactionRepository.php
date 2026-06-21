<?php

namespace App\Repositories\Business\Finance;

use App\Models\Business\Finance\CashBank\CashTransaction;
use App\Models\Business\Finance\CashBank\CashTransactionItem;

class CashTransactionRepository
{
    public function datatable($tenantId, $type)
    {
        return CashTransaction::with(['account', 'creator'])
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc');
    }

    public function findByUuid($tenantId, $uuid)
    {
        return CashTransaction::with(['items.account', 'account', 'creator'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function generateTransactionNumber($tenantId, $branchId, $type)
    {
        $prefix = $type === 'receipt' ? 'CR' : 'CD';
        $dateStr = date('Ym');
        $lastTransaction = CashTransaction::withTrashed()
            ->where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('type', $type)
            ->where('transaction_number', 'like', "{$prefix}-{$dateStr}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$dateStr}-{$newNumber}";
    }

    public function createTransaction(array $data, array $items)
    {
        $transaction = CashTransaction::create($data);
        foreach ($items as $item) {
            $transaction->items()->create($item);
        }
        return $transaction;
    }

    public function updateTransaction(CashTransaction $transaction, array $data, array $items)
    {
        $transaction->update($data);
        $transaction->items()->delete(); // re-create
        foreach ($items as $item) {
            $transaction->items()->create($item);
        }
        return $transaction;
    }

    public function updateStatus(CashTransaction $transaction, $status)
    {
        $transaction->update(['status' => $status]);
    }

    public function delete(CashTransaction $transaction)
    {
        $transaction->items()->delete();
        $transaction->delete();
    }
}
