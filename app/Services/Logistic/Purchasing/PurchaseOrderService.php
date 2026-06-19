<?php

namespace App\Services\Logistic\Purchasing;

use App\Repositories\Logistic\Purchasing\PurchaseOrderRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    protected $repository;

    public function __construct(PurchaseOrderRepository $repository)
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

    public function updateDraft($uuid, array $validatedData)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $userId = Auth::id();
        $items = $validatedData['items'];

        return $this->repository->updateDraft($uuid, $validatedData, $items, $tenantId, $userId);
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return $this->repository->delete($uuid, $tenantId);
    }

    public function submitDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $po = $this->repository->findByUuid($tenantId, $uuid);

        if ($po->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat disubmit.');
        }

        $po->update([
            'status' => 'submitted',
            'updated_by' => Auth::id()
        ]);

        return $po;
    }

    public function approveDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $po = $this->repository->findByUuid($tenantId, $uuid);

        if ($po->status !== 'submitted') {
            throw new \Exception('Hanya dokumen Submitted yang dapat diapprove.');
        }

        $po->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        return $po;
    }

    public function postDocument($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $po = $this->repository->findByUuid($tenantId, $uuid);

        if ($po->status !== 'approved') {
            throw new \Exception('Hanya dokumen Approved yang dapat diposting.');
        }

        DB::beginTransaction();
        try {
            $po->update([
                'status' => 'posted',
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return $po;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
