<?php

namespace App\Repositories\Logistic\Master\ProductionStation;

use App\Models\Logistic\Master\ProductionStation\ProductionStation;
use Illuminate\Support\Facades\Auth;

class ProductionStationRepository
{
    public function getQuery()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return ProductionStation::where('tenant_id', $tenantId);
    }

    public function getAll()
    {
        return $this->getQuery()->get();
    }

    public function getActive()
    {
        return $this->getQuery()->where('status', 'active')->get();
    }

    public function findByUuid($uuid)
    {
        return $this->getQuery()->where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        return ProductionStation::create($data);
    }

    public function update(ProductionStation $station, array $data)
    {
        $station->update($data);
        return $station;
    }

    public function delete(ProductionStation $station)
    {
        return $station->delete();
    }

    public function getMaxId()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        return ProductionStation::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
    }
}
