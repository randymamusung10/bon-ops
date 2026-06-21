<?php

namespace App\Services\Business\Finance;

use App\Models\Business\Finance\GeneralJournal\GeneralJournal;
use App\Models\Business\Finance\GeneralJournal\GeneralJournalItem;
use App\Models\Business\Finance\GeneralLedger\GeneralLedger;
use App\Models\Business\Finance\AccountingPeriod\AccountingPeriod;
use App\Repositories\Business\Finance\GeneralJournalRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GeneralJournalService
{
    protected $repository;

    public function __construct(GeneralJournalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            
            $this->checkAccountingPeriod($tenantId, $data['date']);

            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($data['items'] as $item) {
                $totalDebit += (float) $item['debit'];
                $totalCredit += (float) $item['credit'];
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new \Exception('Total Debit dan Kredit harus seimbang (Balance).');
            }

            $journal = GeneralJournal::create([
                'tenant_id' => $tenantId,
                'branch_id' => Auth::user()->branch_id ?? 1, // Default branch if not set
                'date' => $data['date'],
                'journal_number' => $this->repository->generateJournalNumber($tenantId),
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'draft',
            ]);

            foreach ($data['items'] as $item) {
                GeneralJournalItem::create([
                    'general_journal_id' => $journal->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'description' => $item['description'] ?? null,
                ]);
            }

            return $journal;
        });
    }

    public function updateDraft($uuid, array $data)
    {
        return DB::transaction(function () use ($uuid, $data) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $journal = $this->repository->findByUuid($tenantId, $uuid);

            if ($journal->status !== 'draft') {
                throw new \Exception('Hanya jurnal berstatus Draft yang dapat diedit.');
            }

            $this->checkAccountingPeriod($tenantId, $data['date']);

            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($data['items'] as $item) {
                $totalDebit += (float) $item['debit'];
                $totalCredit += (float) $item['credit'];
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new \Exception('Total Debit dan Kredit harus seimbang (Balance).');
            }

            $updateData = [
                'date' => $data['date'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ];

            if (array_key_exists('attachment_path', $data)) {
                $updateData['attachment_path'] = $data['attachment_path'];
            }

            $journal->update($updateData);

            // Delete old items and recreate
            $journal->items()->delete();

            foreach ($data['items'] as $item) {
                GeneralJournalItem::create([
                    'general_journal_id' => $journal->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'description' => $item['description'] ?? null,
                ]);
            }

            return $journal;
        });
    }

    public function submitDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $journal = $this->repository->findByUuid($tenantId, $uuid);

        if ($journal->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang bisa di-submit.');
        }

        $journal->update(['status' => 'submitted']);
        return $journal;
    }

    public function approveDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $journal = $this->repository->findByUuid($tenantId, $uuid);

        if ($journal->status !== 'submitted') {
            throw new \Exception('Dokumen belum di-submit.');
        }

        $journal->update(['status' => 'approved']);
        return $journal;
    }

    public function postDocument($uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $journal = $this->repository->findByUuid($tenantId, $uuid);

            if ($journal->status !== 'approved') {
                throw new \Exception('Dokumen belum di-approve.');
            }

            $this->checkAccountingPeriod($tenantId, $journal->date);

            // Integrasi ke Buku Besar (General Ledger)
            foreach ($journal->items as $item) {
                GeneralLedger::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $journal->branch_id,
                    'date' => $journal->date,
                    'source_type' => GeneralJournal::class,
                    'source_id' => $journal->id,
                    'chart_of_account_id' => $item->chart_of_account_id,
                    'description' => $item->description ?? $journal->notes,
                    'debit' => $item->debit,
                    'credit' => $item->credit,
                ]);
            }

            $journal->update(['status' => 'posted']);

            return $journal;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $journal = $this->repository->findByUuid($tenantId, $uuid);

        if ($journal->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat dihapus.');
        }

        $journal->delete();
    }

    protected function checkAccountingPeriod($tenantId, $date)
    {
        if (!AccountingPeriod::isOpen($tenantId, Carbon::parse($date))) {
            throw new \Exception('Tanggal jurnal berada pada periode akuntansi yang sudah ditutup (Tutup Buku).');
        }
    }
}
