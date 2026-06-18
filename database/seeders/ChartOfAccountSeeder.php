<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 1;
        $companyId = 1;
        $adminId = 1;

        $coas = [
            // Aset (1)
            ['code' => '1000', 'name' => 'Aset', 'account_type' => 'asset', 'is_header' => true, 'parent_code' => null],
            ['code' => '1100', 'name' => 'Aset Lancar', 'account_type' => 'asset', 'is_header' => true, 'parent_code' => '1000'],
            ['code' => '1110', 'name' => 'Kas & Bank', 'account_type' => 'asset', 'is_header' => true, 'parent_code' => '1100'],
            ['code' => '1111', 'name' => 'Kas Kecil', 'account_type' => 'asset', 'is_header' => false, 'parent_code' => '1110'],
            ['code' => '1112', 'name' => 'Bank BCA', 'account_type' => 'asset', 'is_header' => false, 'parent_code' => '1110'],
            ['code' => '1120', 'name' => 'Piutang Usaha', 'account_type' => 'asset', 'is_header' => false, 'parent_code' => '1100'],
            ['code' => '1130', 'name' => 'Persediaan', 'account_type' => 'asset', 'is_header' => true, 'parent_code' => '1100'],
            ['code' => '1131', 'name' => 'Persediaan Bahan Baku', 'account_type' => 'asset', 'is_header' => false, 'parent_code' => '1130'],
            ['code' => '1132', 'name' => 'Persediaan Barang Jadi', 'account_type' => 'asset', 'is_header' => false, 'parent_code' => '1130'],
            
            // Kewajiban (2)
            ['code' => '2000', 'name' => 'Kewajiban', 'account_type' => 'liability', 'is_header' => true, 'parent_code' => null],
            ['code' => '2100', 'name' => 'Kewajiban Jangka Pendek', 'account_type' => 'liability', 'is_header' => true, 'parent_code' => '2000'],
            ['code' => '2110', 'name' => 'Utang Usaha', 'account_type' => 'liability', 'is_header' => false, 'parent_code' => '2100'],
            
            // Ekuitas (3)
            ['code' => '3000', 'name' => 'Ekuitas', 'account_type' => 'equity', 'is_header' => true, 'parent_code' => null],
            ['code' => '3100', 'name' => 'Modal Saham', 'account_type' => 'equity', 'is_header' => false, 'parent_code' => '3000'],
            ['code' => '3200', 'name' => 'Laba Ditahan', 'account_type' => 'equity', 'is_header' => false, 'parent_code' => '3000'],
            
            // Pendapatan (4)
            ['code' => '4000', 'name' => 'Pendapatan', 'account_type' => 'revenue', 'is_header' => true, 'parent_code' => null],
            ['code' => '4100', 'name' => 'Pendapatan Usaha', 'account_type' => 'revenue', 'is_header' => true, 'parent_code' => '4000'],
            ['code' => '4110', 'name' => 'Penjualan Produk', 'account_type' => 'revenue', 'is_header' => false, 'parent_code' => '4100'],
            ['code' => '4120', 'name' => 'Pendapatan Jasa', 'account_type' => 'revenue', 'is_header' => false, 'parent_code' => '4100'],
            
            // Beban (5)
            ['code' => '5000', 'name' => 'Beban', 'account_type' => 'expense', 'is_header' => true, 'parent_code' => null],
            ['code' => '5100', 'name' => 'Harga Pokok Penjualan (HPP)', 'account_type' => 'expense', 'is_header' => false, 'parent_code' => '5000'],
            ['code' => '5200', 'name' => 'Beban Operasional', 'account_type' => 'expense', 'is_header' => true, 'parent_code' => '5000'],
            ['code' => '5210', 'name' => 'Beban Gaji', 'account_type' => 'expense', 'is_header' => false, 'parent_code' => '5200'],
            ['code' => '5220', 'name' => 'Beban Listrik & Air', 'account_type' => 'expense', 'is_header' => false, 'parent_code' => '5200'],
        ];

        $insertedCoas = [];

        foreach ($coas as $coaData) {
            $parentId = null;
            if ($coaData['parent_code']) {
                $parentId = $insertedCoas[$coaData['parent_code']] ?? null;
            }

            $coa = \App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::create([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $coaData['code'],
                'name' => $coaData['name'],
                'account_type' => $coaData['account_type'],
                'is_header' => $coaData['is_header'],
                'parent_id' => $parentId,
                'status' => 'active',
                'created_by' => $adminId,
            ]);

            $insertedCoas[$coa->code] = $coa->id;
        }
    }
}
