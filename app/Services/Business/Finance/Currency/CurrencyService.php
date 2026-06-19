<?php

namespace App\Services\Business\Finance\Currency;

use App\Models\Business\Finance\Currency\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CurrencyService
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

        // Clean formatting for exchange_rate if needed
        if (isset($data['exchange_rate'])) {
            $data['exchange_rate'] = \App\Helpers\NumberHelper::parse($data['exchange_rate']);
        }

        return DB::transaction(function () use ($data) {
            return Currency::create($data);
        });
    }

    public function update($uuid, array $data)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $currency = Currency::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        // Clean formatting for exchange_rate if needed
        if (isset($data['exchange_rate'])) {
            $data['exchange_rate'] = \App\Helpers\NumberHelper::parse($data['exchange_rate']);
        }

        return DB::transaction(function () use ($currency, $data) {
            $currency->update($data);
            return $currency;
        });
    }

    public function delete($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $currency = Currency::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return DB::transaction(function () use ($currency) {
            $currency->delete();
            return true;
        });
    }
}
