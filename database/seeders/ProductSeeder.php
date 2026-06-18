<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        $catRaw = \App\Models\Logistic\Master\ProductCategory\ProductCategory::where('code', 'CAT-RAW')->first()->id;
        $catBev = \App\Models\Logistic\Master\ProductCategory\ProductCategory::where('code', 'CAT-BEV')->first()->id;
        $unitKg = \App\Models\Logistic\Master\Unit\Unit::where('code', 'KG')->first()->id;
        $unitCup = \App\Models\Logistic\Master\Unit\Unit::where('code', 'CUP')->first()->id;
        $taxPpn = \App\Models\Business\Finance\Tax\Tax::where('code', 'PPN11')->first()->id;

        $invAcc = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('code', '1131')->first()->id;
        $cogsAcc = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('code', '5100')->first()->id;
        $revAcc = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::where('code', '4110')->first()->id;

        $products = [
            [
                'code' => 'PRD-0001',
                'name' => 'Biji Kopi Arabica Gayo',
                'category_id' => $catRaw,
                'unit_id' => $unitKg,
                'type' => 'raw_material',
                'purchase_price' => 120000,
                'selling_price' => 150000,
            ],
            [
                'code' => 'PRD-0002',
                'name' => 'Kopi Susu Aren',
                'category_id' => $catBev,
                'unit_id' => $unitCup,
                'type' => 'finished_good',
                'purchase_price' => 8000,
                'selling_price' => 25000,
            ]
        ];

        foreach ($products as $p) {
            $product = \App\Models\Logistic\Master\Product\Product::create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $p['code'],
                'name' => $p['name'],
                'product_category_id' => $p['category_id'],
                'unit_id' => $p['unit_id'],
                'type' => $p['type'],
                'cost' => $p['purchase_price'],
                'price' => $p['selling_price'],
                'tax_id' => $taxPpn,
                'inventory_account_id' => $invAcc,
                'cogs_account_id' => $cogsAcc,
                'income_account_id' => $revAcc,
                'status' => 'active',
                'created_by' => $adminId,
            ]);

            \App\Models\Logistic\Master\Product\ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_cost' => 0,
                'new_cost' => $p['purchase_price'],
                'old_price' => 0,
                'new_price' => $p['selling_price'],
                'reason' => 'Initial Data (Seeder)',
                'created_by' => $adminId,
            ]);
        }
    }
}
