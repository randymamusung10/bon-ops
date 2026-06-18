<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        $categories = [
            ['code' => 'CAT-RAW', 'name' => 'Bahan Baku'],
            ['code' => 'CAT-BEV', 'name' => 'Minuman (Beverage)'],
            ['code' => 'CAT-FOOD', 'name' => 'Makanan (Food)'],
            ['code' => 'CAT-PKG', 'name' => 'Kemasan (Packaging)'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Logistic\Master\ProductCategory\ProductCategory::create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $cat['code'],
                'name' => $cat['name'],
                'status' => 'active',
                'created_by' => $adminId,
            ]);
        }
    }
}
