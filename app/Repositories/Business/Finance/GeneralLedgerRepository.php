<?php

namespace App\Repositories\Business\Finance;

use App\Models\Business\Finance\GeneralLedger\GeneralLedger;

class GeneralLedgerRepository
{
    public function datatable($tenantId, $accountId, $startDate, $endDate, $beginningBalance = 0, $isDebitNormal = true)
    {
        $query = GeneralLedger::with(['account', 'source'])
            ->where('tenant_id', $tenantId);

        if ($accountId) {
            $query->where('chart_of_account_id', $accountId);
            if ($isDebitNormal) {
                $query->selectRaw("general_ledgers.*, ? + SUM(debit - credit) OVER (ORDER BY date ASC, id ASC) as running_balance", [$beginningBalance]);
            } else {
                $query->selectRaw("general_ledgers.*, ? + SUM(credit - debit) OVER (ORDER BY date ASC, id ASC) as running_balance", [$beginningBalance]);
            }
        } else {
            $query->select('general_ledgers.*');
        }

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $query->orderBy('date', 'asc')->orderBy('id', 'asc');

        return $query;
    }

    public function getBeginningBalance($tenantId, $accountId, $startDate)
    {
        // To accurately get the beginning balance on a specific date,
        // we sum all debit and credit before this date for the given account.
        
        $query = GeneralLedger::where('tenant_id', $tenantId);

        if ($accountId) {
            $query->where('chart_of_account_id', $accountId);
        }

        if ($startDate) {
            $query->whereDate('date', '<', $startDate);
        }

        $result = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')->first();

        return [
            'total_debit' => (float) $result->total_debit,
            'total_credit' => (float) $result->total_credit,
        ];
    }

    public function getPeriodMutation($tenantId, $accountId, $startDate, $endDate)
    {
        $query = GeneralLedger::where('tenant_id', $tenantId);

        if ($accountId) {
            $query->where('chart_of_account_id', $accountId);
        }

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $result = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')->first();

        return [
            'total_debit' => (float) $result->total_debit,
            'total_credit' => (float) $result->total_credit,
        ];
    }
}
