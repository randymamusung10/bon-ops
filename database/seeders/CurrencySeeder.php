<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;

        \App\Models\Business\Finance\Currency\Currency::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'IDR',
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'exchange_rate' => 1.000000,
            'status' => 'active'
        ]);

        \App\Models\Business\Finance\Currency\Currency::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 15500.000000,
            'status' => 'active'
        ]);
    }
}
