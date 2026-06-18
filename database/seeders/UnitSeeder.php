<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        $units = [
            ['code' => 'PCS', 'name' => 'Pieces'],
            ['code' => 'KG', 'name' => 'Kilogram'],
            ['code' => 'GR', 'name' => 'Gram'],
            ['code' => 'LTR', 'name' => 'Liter'],
            ['code' => 'ML', 'name' => 'Milliliter'],
            ['code' => 'CUP', 'name' => 'Cup'],
        ];

        foreach ($units as $unit) {
            \App\Models\Logistic\Master\Unit\Unit::create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $unit['code'],
                'name' => $unit['name'],
                'status' => 'active',
                'created_by' => $adminId,
            ]);
        }
    }
}
