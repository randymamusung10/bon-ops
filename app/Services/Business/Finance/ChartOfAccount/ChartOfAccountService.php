<?php

namespace App\Services\Business\Finance\ChartOfAccount;

use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChartOfAccountService
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

        $data['is_header'] = $data['is_header'] ?? false;
        
        // Ensure detail accounts have parent if needed, or just let it pass
        if ($data['is_header']) {
            $data['parent_id'] = null; // Maybe a header doesn't have parent, or it can. We allow it.
        }

        return DB::transaction(function () use ($data) {
            return ChartOfAccount::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $coa = ChartOfAccount::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        $data['is_header'] = $data['is_header'] ?? false;

        return DB::transaction(function () use ($coa, $data) {
            $coa->update($data);
            return $coa;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $coa = ChartOfAccount::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($coa) {
            $coa->delete();
            return true;
        });
    }

    public function getForSelect2($search = '', $type = null, $onlyDetail = false, $onlyHeader = false)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $query = ChartOfAccount::where('tenant_id', $tenantId)->where('status', 'active');

        if ($onlyDetail) {
            $query->where('is_header', false);
        }

        if ($onlyHeader) {
            $query->where('is_header', true);
        }

        if ($type) {
            $query->where('account_type', $type);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $coas = $query->limit(20)->get();

        $results = [];
        foreach ($coas as $coa) {
            $results[] = [
                'id' => $coa->id,
                'text' => '[' . $coa->code . '] ' . $coa->name
            ];
        }

        return $results;
    }
}
