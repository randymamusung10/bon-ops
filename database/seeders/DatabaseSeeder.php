<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Company;
use App\Models\Logistic\Master\Branch\Branch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Tenant Default
        $tenant = Tenant::create([
            'name' => 'PT Kopi Nusantara',
            'subdomain' => 'kopinusantara',
            'status' => 'active',
        ]);

        // 2. Buat Company Default
        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name' => 'PT Kopi Nusantara',
            'status' => 'active',
        ]);

        // 3. Buat Admin User (sementara belum terikat branch)
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'name' => 'Developer Admin',
            'email' => 'dev@bon.com',
            'password' => bcrypt('password'),
        ]);

        // 4. Buat Cabang (Branch) Default
        $branch1 = Branch::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'code' => 'JKT-01',
            'name' => 'Jakarta Pusat Outlet',
            'city' => 'Jakarta Pusat',
            'address' => 'Jl. MH Thamrin No. 1',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $branch2 = Branch::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'code' => 'BDG-01',
            'name' => 'Bandung Dago',
            'city' => 'Bandung',
            'address' => 'Jl. Ir. H. Juanda Dago No. 10',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $branch3 = Branch::create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'code' => 'SBY-01',
            'name' => 'Surabaya Gubeng',
            'city' => 'Surabaya',
            'address' => 'Jl. Gubeng Masjid No. 5',
            'status' => 'inactive',
            'created_by' => $admin->id,
        ]);

        // 5. Update Admin User dengan Default Branch
        $admin->update([
            'branch_id' => $branch1->id,
        ]);

        // 5.5. Roles & Permissions (System)
        $this->call([
            SystemRoleUserSeeder::class,
        ]);

        // 6. Master Data Financial (Independent)
        $this->call([
            CurrencySeeder::class,
            TaxSeeder::class,
            ChartOfAccountSeeder::class,
        ]);

        // 7. Master Data Logistik Dasar
        $this->call([
            WarehouseSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            ProductCategorySeeder::class,
            UnitSeeder::class,
        ]);

        // 8. Master Produk Terintegrasi (Bergantung pada Category, Unit, Tax, COA)
        $this->call([
            ProductSeeder::class,
            RecipeSeeder::class,
            InventoryOpnameWasteSeeder::class,
            PosDemoSeeder::class,
        ]);
    }
}
