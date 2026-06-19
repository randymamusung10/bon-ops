<?php

namespace App\Services\Logistic\Master\Company;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CompanyService
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $company = Company::create([
                'uuid' => Str::uuid(),
                'tenant_id' => Auth::user()->tenant_id ?? 1,
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $company = Company::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

            $company->update([
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(string $uuid)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $company = Company::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();
            $company->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getForSelect2($search = '')
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = Company::where('tenant_id', $tenantId)->where('status', 'active');

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->limit(20)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        });
    }
}
