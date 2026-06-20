<?php

namespace App\Services\Logistic\Master\ProductionStation;

use App\Repositories\Logistic\Master\ProductionStation\ProductionStationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionStationService
{
    protected $repository;

    public function __construct(ProductionStationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getActive()
    {
        return $this->repository->getActive();
    }

    public function findByUuid($uuid)
    {
        return $this->repository->findByUuid($uuid);
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $data['tenant_id'] = $tenantId;
        $data['company_id'] = $user->company_id ?? 1;

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return DB::transaction(function () use ($data) {
            $maxId = $this->repository->getMaxId();
            $data['code'] = 'STN-' . date('ym') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            return $this->repository->create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $station = $this->repository->findByUuid($uuid);
        return DB::transaction(function () use ($station, $data) {
            return $this->repository->update($station, $data);
        });
    }

    public function delete($uuid)
    {
        $station = $this->repository->findByUuid($uuid);
        return DB::transaction(function () use ($station) {
            return $this->repository->delete($station);
        });
    }
}
