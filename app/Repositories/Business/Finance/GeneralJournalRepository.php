<?php

namespace App\Repositories\Business\Finance;

use App\Models\Business\Finance\GeneralJournal\GeneralJournal;

class GeneralJournalRepository
{
    public function getBaseQuery($tenantId)
    {
        return GeneralJournal::with(['branch', 'creator'])
            ->where('tenant_id', $tenantId);
    }

    public function datatable($tenantId)
    {
        return $this->getBaseQuery($tenantId);
    }

    public function findByUuid($tenantId, $uuid)
    {
        return GeneralJournal::with(['items.account', 'branch'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function generateJournalNumber($tenantId)
    {
        $prefix = 'JV-' . date('Ym');
        $lastJournal = GeneralJournal::withTrashed()
            ->where('tenant_id', $tenantId)
            ->where('journal_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastJournal) {
            return $prefix . '-0001';
        }

        $lastNumber = intval(substr($lastJournal->journal_number, -4));
        return $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
