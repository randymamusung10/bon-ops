<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Master\ProductionStation\ProductionStation;
use App\Models\Logistic\Master\Recipe\Recipe;
use App\Models\Logistic\Master\Recipe\RecipeItem;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        // 1. Seed Production Stations
        $barista = ProductionStation::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'uuid' => (string) Str::uuid(),
            'code' => 'STN-BARISTA',
            'name' => 'Barista Station',
            'description' => 'Area pembuatan kopi dan minuman dingin/panas.',
            'status' => 'active',
            'created_by' => $adminId,
        ]);

        $kitchen = ProductionStation::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'uuid' => (string) Str::uuid(),
            'code' => 'STN-KITCHEN',
            'name' => 'Kitchen Utama',
            'description' => 'Area memasak makanan utama dan makanan ringan.',
            'status' => 'active',
            'created_by' => $adminId,
        ]);

        // 2. Find target product and ingredient
        $targetProduct = Product::where('code', 'PRD-0002')->first(); // Kopi Susu Aren
        $ingredient = Product::where('code', 'PRD-0001')->first(); // Biji Kopi Arabica Gayo
        $unitKg = Unit::where('code', 'KG')->first();

        if ($targetProduct && $ingredient && $unitKg) {
            // Seed Recipe
            $recipe = Recipe::create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'product_id' => $targetProduct->id,
                'production_station_id' => $barista->id,
                'uuid' => (string) Str::uuid(),
                'code' => 'RCP-2606-0001',
                'name' => 'Resep Standard Kopi Susu Aren',
                'quantity' => 1.0000,
                'status' => 'active',
                'created_by' => $adminId,
            ]);

            // Seed Recipe Item
            RecipeItem::create([
                'recipe_id' => $recipe->id,
                'product_id' => $ingredient->id,
                'quantity' => 0.0200, // 20 grams
                'unit_id' => $unitKg->id,
                'cost' => 0.0200 * $ingredient->cost, // 2400
            ]);
        }
    }
}
