<?php

namespace App\Services\Business\Finance\Tax;

use App\Models\Business\Finance\Tax\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaxService
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

        if (isset($data['rate_percentage'])) {
            $data['rate_percentage'] = \App\Helpers\NumberHelper::parse($data['rate_percentage']);
        }

        return DB::transaction(function () use ($data) {
            return Tax::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $tax = Tax::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        if (isset($data['rate_percentage'])) {
            $data['rate_percentage'] = \App\Helpers\NumberHelper::parse($data['rate_percentage']);
        }

        return DB::transaction(function () use ($tax, $data) {
            $tax->update($data);
            return $tax;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $tax = Tax::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($tax) {
            $tax->delete();
            return true;
        });
    }

    public function getForSelect2($search = '')
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $query = Tax::where('tenant_id', $tenantId)->where('status', 'active');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $taxes = $query->limit(20)->get();

        $results = [];
        foreach ($taxes as $tax) {
            $results[] = [
                'id' => $tax->id,
                'text' => '[' . $tax->code . '] ' . $tax->name . ' (' . (float)$tax->rate_percentage . '%)'
            ];
        }

        return $results;
    }
}
