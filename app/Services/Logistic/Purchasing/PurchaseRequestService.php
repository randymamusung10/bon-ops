<?php

namespace App\Services\Logistic\Purchasing;

use App\Repositories\Logistic\Purchasing\PurchaseRequestRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestService
{
    protected $repository;

    public function __construct(PurchaseRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $validatedData)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $userId = Auth::id();
        $items = $validatedData['items'];

        return $this->repository->createDraft($validatedData, $items, $tenantId, $userId);
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return $this->repository->delete($uuid, $tenantId);
    }

    public function submitDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $pr = $this->repository->findByUuid($tenantId, $uuid);

        if ($pr->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat disubmit.');
        }

        $pr->update([
            'status' => 'submitted',
            'updated_by' => Auth::id()
        ]);

        return $pr;
    }

    public function approveDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $pr = $this->repository->findByUuid($tenantId, $uuid);

        if ($pr->status !== 'submitted') {
            throw new \Exception('Hanya dokumen Submitted yang dapat diapprove.');
        }

        $pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        return $pr;
    }

    public function postDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $pr = $this->repository->findByUuid($tenantId, $uuid);

        if ($pr->status !== 'approved') {
            throw new \Exception('Hanya dokumen Approved yang dapat diposting.');
        }

        $pr->update([
            'status' => 'posted',
            'updated_by' => Auth::id()
        ]);

        return $pr;
    }
}
