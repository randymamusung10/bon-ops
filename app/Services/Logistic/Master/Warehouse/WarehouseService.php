<?php

namespace App\Services\Logistic\Master\Warehouse;

use App\Models\Logistic\Master\Warehouse\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WarehouseService
{
    public function create(array $data)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $data['tenant_id'] = $tenantId;
        $data['company_id'] = $user->company_id ?? 1;

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return DB::transaction(function () use ($data, $tenantId) {
            $maxId = Warehouse::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'WRH-' . date('ym') . '-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
            return Warehouse::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $warehouse = Warehouse::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($warehouse, $data) {
            $warehouse->update($data);
            return $warehouse;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $warehouse = Warehouse::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($warehouse) {
            $warehouse->delete();
            return true;
        });
    }
}
