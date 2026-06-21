<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan Permissions
        $permissions = [
            'view_dashboard',
            
            // System Settings
            'manage_users',
            'manage_roles',
            'manage_branch_config',
            
            // Master Data
            'manage_company',
            'manage_branch',
            'manage_product',
            'manage_inventory',
            
            // POS
            'access_pos',
            'manage_shift',
            'refund_transaction'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Definisikan Roles dan Assign Permissions
        
        // Super Admin (Semua akses)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Manager Cabang
        $managerRole = Role::firstOrCreate(['name' => 'Branch Manager']);
        $managerRole->givePermissionTo([
            'view_dashboard',
            'manage_users',
            'manage_product',
            'manage_inventory',
            'access_pos',
            'refund_transaction'
        ]);

        // Kasir
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier']);
        $cashierRole->givePermissionTo([
            'access_pos',
            'manage_shift'
        ]);

        // 3. Assign Role ke User yang sudah ada
        $admin = User::where('email', 'dev@bon.com')->first();
        if ($admin) {
            $admin->assignRole('Super Admin');
        }

        // 4. Buat User Testing Tambahan
        $manager = User::firstOrCreate([
            'email' => 'manager@bon.com'
        ], [
            'name' => 'Manager Test',
            'password' => bcrypt('password'),
            'tenant_id' => 1,
            'company_id' => 1,
            'branch_id' => 1
        ]);
        $manager->assignRole('Branch Manager');

        $kasir = User::firstOrCreate([
            'email' => 'kasir@bon.com'
        ], [
            'name' => 'Kasir Test',
            'password' => bcrypt('password'),
            'tenant_id' => 1,
            'company_id' => 1,
            'branch_id' => 1
        ]);
        $kasir->assignRole('Cashier');
    }
}
