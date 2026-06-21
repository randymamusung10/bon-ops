<?php

namespace App\Services\Business\Finance;

use App\Models\Business\Finance\CashBank\CashTransaction;
use App\Models\Business\Finance\GeneralLedger\GeneralLedger;
use App\Models\Business\Finance\AccountingPeriod\AccountingPeriod;
use App\Repositories\Business\Finance\CashTransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashTransactionService
{
    protected $repository;

    public function __construct(CashTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $branchId = Auth::user()->branch_id ?? 1;
            
            $this->checkAccountingPeriod($tenantId, $data['date']);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += (float) $item['amount'];
            }

            $transactionData = [
                'tenant_id' => $tenantId,
                'company_id' => Auth::user()->company_id ?? 1,
                'branch_id' => $branchId,
                'type' => $data['type'],
                'date' => $data['date'],
                'transaction_number' => $this->repository->generateTransactionNumber($tenantId, $branchId, $data['type']),
                'account_id' => $data['account_id'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'attachment_path' => $data['attachment_path'] ?? null,
                'created_by' => Auth::id(),
            ];

            return $this->repository->createTransaction($transactionData, $data['items']);
        });
    }

    public function updateDraft($uuid, array $data)
    {
        return DB::transaction(function () use ($uuid, $data) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $transaction = $this->repository->findByUuid($tenantId, $uuid);

            if ($transaction->status !== 'draft') {
                throw new \Exception('Hanya transaksi berstatus Draft yang dapat diedit.');
            }

            $this->checkAccountingPeriod($tenantId, $data['date']);

            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += (float) $item['amount'];
            }

            $updateData = [
                'date' => $data['date'],
                'account_id' => $data['account_id'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'] ?? null,
                'total_amount' => $totalAmount,
                'updated_by' => Auth::id(),
            ];

            if (array_key_exists('attachment_path', $data)) {
                $updateData['attachment_path'] = $data['attachment_path'];
            }

            return $this->repository->updateTransaction($transaction, $updateData, $data['items']);
        });
    }

    public function submitDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transaction = $this->repository->findByUuid($tenantId, $uuid);

        if ($transaction->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang bisa di-submit.');
        }

        $this->repository->updateStatus($transaction, 'submitted');
        return $transaction;
    }

    public function approveDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transaction = $this->repository->findByUuid($tenantId, $uuid);

        if ($transaction->status !== 'submitted') {
            throw new \Exception('Dokumen belum di-submit.');
        }

        $this->repository->updateStatus($transaction, 'approved');
        return $transaction;
    }

    public function postDocument($uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $transaction = $this->repository->findByUuid($tenantId, $uuid);

            if ($transaction->status !== 'approved') {
                throw new \Exception('Dokumen belum di-approve.');
            }

            $this->checkAccountingPeriod($tenantId, $transaction->date);

            // Integrasi ke Buku Besar (General Ledger)
            $isReceipt = $transaction->type === 'receipt';

            // Jurnal untuk Header (Kas/Bank)
            GeneralLedger::create([
                'tenant_id' => $tenantId,
                'branch_id' => $transaction->branch_id,
                'date' => $transaction->date,
                'source_type' => CashTransaction::class,
                'source_id' => $transaction->id,
                'chart_of_account_id' => $transaction->account_id,
                'description' => $transaction->description ?: "Transaksi Kas: {$transaction->transaction_number}",
                'debit' => $isReceipt ? $transaction->total_amount : 0,
                'credit' => $isReceipt ? 0 : $transaction->total_amount,
            ]);

            // Jurnal untuk Items (Akun Lawan)
            foreach ($transaction->items as $item) {
                GeneralLedger::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $transaction->branch_id,
                    'date' => $transaction->date,
                    'source_type' => CashTransaction::class,
                    'source_id' => $transaction->id,
                    'chart_of_account_id' => $item->account_id,
                    'description' => $item->description,
                    'debit' => $isReceipt ? 0 : $item->amount,
                    'credit' => $isReceipt ? $item->amount : 0,
                ]);
            }

            $this->repository->updateStatus($transaction, 'posted');

            return $transaction;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $transaction = $this->repository->findByUuid($tenantId, $uuid);

        if ($transaction->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat dihapus.');
        }

        $this->repository->delete($transaction);
    }

    protected function checkAccountingPeriod($tenantId, $date)
    {
        if (!AccountingPeriod::isOpen($tenantId, Carbon::parse($date))) {
            throw new \Exception('Tanggal transaksi berada pada periode akuntansi yang sudah ditutup (Tutup Buku).');
        }
    }
}
