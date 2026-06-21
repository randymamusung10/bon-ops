<?php

namespace App\Services\Business\Finance;

use App\Repositories\Business\Finance\GeneralLedgerRepository;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;

class GeneralLedgerService
{
    protected $repository;

    public function __construct(GeneralLedgerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSummary($tenantId, $accountId, $startDate, $endDate)
    {
        $beginning = $this->repository->getBeginningBalance($tenantId, $accountId, $startDate);
        $mutation = $this->repository->getPeriodMutation($tenantId, $accountId, $startDate, $endDate);

        $beginningBalance = 0;
        $endingBalance = 0;
        $isDebitNormal = true;
        $account = null;

        if ($accountId) {
            $account = ChartOfAccount::where('tenant_id', $tenantId)->find($accountId);
            if (!$account) {
                throw new \Exception("Akun tidak ditemukan.");
            }

            $isDebitNormal = in_array($account->account_type, ['asset', 'expense']);
            
            if ($isDebitNormal) {
                $beginningBalance = $beginning['total_debit'] - $beginning['total_credit'];
                $endingBalance = $beginningBalance + $mutation['total_debit'] - $mutation['total_credit'];
            } else {
                $beginningBalance = $beginning['total_credit'] - $beginning['total_debit'];
                $endingBalance = $beginningBalance + $mutation['total_credit'] - $mutation['total_debit'];
            }
        } else {
            // When viewing all accounts, the beginning balance is technically 0 if the system is balanced.
            // But we can show the absolute sum or just 0. Let's just show 0 for balances, and the actual total for debit/credit.
            $beginningBalance = $beginning['total_debit'] - $beginning['total_credit']; // Should be 0
            $endingBalance = $beginningBalance + $mutation['total_debit'] - $mutation['total_credit']; // Should be 0
        }

        return [
            'account' => $account,
            'beginning_balance' => $beginningBalance,
            'total_debit' => $mutation['total_debit'],
            'total_credit' => $mutation['total_credit'],
            'ending_balance' => $endingBalance,
            'is_debit_normal' => $isDebitNormal
        ];
    }
}
