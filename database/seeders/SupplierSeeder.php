<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        \App\Models\Logistic\Master\Supplier\Supplier::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'SUP-001',
            'name' => 'PT Pemasok Biji Kopi',
            'phone' => '081234567890',
            'address' => 'Jl. Kopi No. 1, Jakarta',
            'status' => 'active',
            'created_by' => $adminId,
        ]);

        \App\Models\Logistic\Master\Supplier\Supplier::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'code' => 'SUP-002',
            'name' => 'CV Kemasan Plastik',
            'phone' => '089876543210',
            'address' => 'Jl. Plastik No. 2, Bandung',
            'status' => 'active',
            'created_by' => $adminId,
        ]);
    }
}
