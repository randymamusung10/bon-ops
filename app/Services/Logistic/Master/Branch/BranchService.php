<?php

namespace App\Services\Logistic\Master\Branch;

use App\Models\Logistic\Master\Branch\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BranchService
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
            $maxId = Branch::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'BRC-' . date('ym') . '-' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
            return Branch::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branch = Branch::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($branch, $data) {
            $branch->update($data);
            return $branch;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branch = Branch::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($branch) {
            $branch->delete();
            return true;
        });
    }

    public function getForSelect2(?string $search)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $branches = Branch::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get();

        return $branches->map(function($branch) {
            return [
                'id' => $branch->id,
                'text' => '[' . $branch->code . '] ' . $branch->name
            ];
        });
    }
}
