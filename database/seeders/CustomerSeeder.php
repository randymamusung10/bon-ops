<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        \App\Models\Logistic\Master\Customer\Customer::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'CUST-001',
            'name' => 'Pelanggan Walk-in',
            'phone' => '-',
            'status' => 'active',
            'created_by' => $adminId,
        ]);

        \App\Models\Logistic\Master\Customer\Customer::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'CUST-002',
            'name' => 'PT Pelanggan Tetap',
            'phone' => '08111222333',
            'status' => 'active',
            'created_by' => $adminId,
        ]);
    }
}
