<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;
        
        // Asumsi Branch Jakarta ID = 1, Bandung = 2
        \App\Models\Logistic\Master\Warehouse\Warehouse::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'branch_id' => 1,
            'code' => 'WH-JKT-01',
            'name' => 'Gudang Pusat Jakarta',
            'status' => 'active',
            'created_by' => $adminId,
        ]);

        \App\Models\Logistic\Master\Warehouse\Warehouse::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'branch_id' => 2,
            'code' => 'WH-BDG-01',
            'name' => 'Gudang Cabang Bandung',
            'status' => 'active',
            'created_by' => $adminId,
        ]);
    }
}
