<?php

namespace App\Services\MasterData;

use App\Models\MasterData\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplierService
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
            $maxId = Supplier::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'SUPP-' . date('ym') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            return Supplier::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $supplier = Supplier::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($supplier, $data) {
            $supplier->update($data);
            return $supplier;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $supplier = Supplier::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($supplier) {
            $supplier->delete();
            return true;
        });
    }
}
