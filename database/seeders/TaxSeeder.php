<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;

        \App\Models\Business\Finance\Tax\Tax::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'PPN11',
            'name' => 'PPN 11%',
            'rate_percentage' => 11.00,
            'status' => 'active'
        ]);

        \App\Models\Business\Finance\Tax\Tax::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'PB1',
            'name' => 'Pajak Restoran 10%',
            'rate_percentage' => 10.00,
            'status' => 'active'
        ]);
    }
}
