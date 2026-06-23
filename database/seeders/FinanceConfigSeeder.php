<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\System\Settings\FinanceConfig;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;

class FinanceConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan akun-akun yang diperlukan untuk POS secara fuzzy
        $cashAccount = ChartOfAccount::where('name', 'like', '%Kas%')->first() 
            ?? ChartOfAccount::where('name', 'like', '%Bank%')->first();
            
        $salesRevenueAccount = ChartOfAccount::where('name', 'like', '%Pendapatan Penjualan%')->first()
            ?? ChartOfAccount::where('name', 'like', '%Pendapatan%')->first();
            
        $taxPayableAccount = ChartOfAccount::where('name', 'like', '%PPN Keluaran%')->first()
            ?? ChartOfAccount::where('name', 'like', '%Hutang Pajak%')->first();
            
        $cogsAccount = ChartOfAccount::where('name', 'like', '%Harga Pokok Penjualan%')->first()
            ?? ChartOfAccount::where('name', 'like', '%HPP%')->first();
            
        $inventoryAccount = ChartOfAccount::where('name', 'like', '%Persediaan%')->first();

        // Cek jika FinanceConfig sudah ada, kalau belum buat baru
        $config = FinanceConfig::first();
        if (!$config) {
            $config = new FinanceConfig();
            $config->tenant_id = 1;
            $config->branch_id = 1;
        }

        // Mapping akun-akun yang ditemukan
        $config->cash_account_id = $cashAccount ? $cashAccount->id : null;
        $config->sales_revenue_account_id = $salesRevenueAccount ? $salesRevenueAccount->id : null;
        $config->tax_payable_account_id = $taxPayableAccount ? $taxPayableAccount->id : null;
        $config->cogs_account_id = $cogsAccount ? $cogsAccount->id : null;
        $config->inventory_account_id = $inventoryAccount ? $inventoryAccount->id : null;
        
        $config->save();

        $this->command->info('FinanceConfig berhasil diisi. Silakan cek menu Konfigurasi Keuangan.');
    }
}
