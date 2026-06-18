<?php

namespace App\Services\Logistic\Master\Customer;

use App\Models\Logistic\Master\Customer\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    public function getAllByTenant($tenantId)
    {
        return Customer::with(['currency', 'tax', 'accountReceivable'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->paginate(15);
    }

    public function create(array $data)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? 1;
        $data['tenant_id'] = $tenantId;
        $data['company_id'] = $user->company_id ?? 1;

        return DB::transaction(function () use ($data, $tenantId) {
            $maxId = Customer::where('tenant_id', $tenantId)->withTrashed()->max('id') ?? 0;
            $data['code'] = 'CUST-' . date('ym') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
            return Customer::create($data);
        });
    }

    public function update(string $uuid, array $data)
    {
        return DB::transaction(function () use ($uuid, $data) {
            $customer = Customer::where('uuid', $uuid)->firstOrFail();
            $customer->update($data);
            return $customer;
        });
    }

    public function delete(string $uuid)
    {
        return DB::transaction(function () use ($uuid) {
            $customer = Customer::where('uuid', $uuid)->firstOrFail();
            $customer->delete();
            return true;
        });
    }
}
