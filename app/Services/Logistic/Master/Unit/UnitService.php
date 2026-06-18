<?php

namespace App\Services\Logistic\Master\Unit;

use App\Models\Logistic\Master\Unit\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UnitService
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
            $maxId = Unit::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'UOM-' . date('ym') . '-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
            return Unit::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $unit = Unit::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($unit, $data) {
            $unit->update($data);
            return $unit;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $unit = Unit::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($unit) {
            $unit->delete();
            return true;
        });
    }
    
    public function getForSelect2(?string $search)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $units = Unit::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get();

        return $units->map(function($unit) {
            return [
                'id' => $unit->id,
                'text' => '[' . $unit->code . '] ' . $unit->name
            ];
        });
    }
}
