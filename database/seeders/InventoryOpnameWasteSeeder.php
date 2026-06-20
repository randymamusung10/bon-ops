<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Inventory\StockOpname;
use App\Models\Logistic\Inventory\StockOpnameItem;
use App\Models\Logistic\Inventory\StockWaste;
use App\Models\Logistic\Inventory\StockWasteItem;
use App\Services\Logistic\Inventory\StockOpnameService;
use App\Services\Logistic\Inventory\StockWasteService;
use Illuminate\Support\Facades\Auth;

class InventoryOpnameWasteSeeder extends Seeder
{
    public function run()
    {
        // Login as default admin user (ID 1)
        Auth::loginUsingId(1);

        $tenantId = 1;
        $branch = Branch::where('tenant_id', $tenantId)->first();
        $warehouse = Warehouse::where('tenant_id', $tenantId)->first();
        
        if (!$branch || !$warehouse) {
            $this->command->error('Seeder Gagal: Cabang atau Gudang default tidak ditemukan. Jalankan InventoryDemoSeeder terlebih dahulu.');
            return;
        }

        $products = Product::where('tenant_id', $tenantId)->limit(3)->get();
        if ($products->isEmpty()) {
            $this->command->error('Seeder Gagal: Produk tidak ditemukan. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        $opnameService = app(StockOpnameService::class);
        $wasteService = app(StockWasteService::class);

        // 1. Buat Draf Stock Opname
        $opnameDraft = StockOpname::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'document_number' => 'SO-DRAFT-' . time(),
            'date' => now()->toDateString(),
            'status' => 'draft',
            'notes' => 'Draft opname mingguan rutin',
            'created_by' => 1
        ]);

        foreach ($products as $product) {
            StockOpnameItem::create([
                'stock_opname_id' => $opnameDraft->id,
                'product_id' => $product->id,
                'system_qty' => 100, // asumsikan
                'actual_qty' => 98.5,
                'difference' => -1.5,
                'notes' => 'Penyusutan bahan baku alami'
            ]);
        }

        // 2. Buat & Post Stock Opname (Selesai Posting)
        $opnamePosted = StockOpname::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'document_number' => 'SO-POSTED-' . time(),
            'date' => now()->subDays(3)->toDateString(),
            'status' => 'draft',
            'notes' => 'Stok Opname Awal Bulan (Telah Diposting)',
            'created_by' => 1
        ]);

        foreach ($products as $product) {
            StockOpnameItem::create([
                'stock_opname_id' => $opnamePosted->id,
                'product_id' => $product->id,
                'system_qty' => 50,
                'actual_qty' => 52,
                'difference' => 2,
                'notes' => 'Kelebihan kiriman dari supplier'
            ]);
        }

        // Jalankan alur workflow posting untuk opnamePosted
        $opnameService->submitDocument($opnamePosted->uuid);
        $opnameService->approveDocument($opnamePosted->uuid);
        $opnameService->postDocument($opnamePosted->uuid);


        // 3. Buat Draf Stock Waste
        $wasteDraft = StockWaste::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'document_number' => 'SW-DRAFT-' . time(),
            'date' => now()->toDateString(),
            'status' => 'draft',
            'notes' => 'Draft pembuangan barang rusak pengiriman',
            'created_by' => 1
        ]);

        foreach ($products as $product) {
            StockWasteItem::create([
                'stock_waste_id' => $wasteDraft->id,
                'product_id' => $product->id,
                'qty' => 2,
                'reason' => 'Damaged',
                'cost' => $product->cost ?? 15000
            ]);
        }

        // 4. Buat & Post Stock Waste (Selesai Posting)
        $wastePosted = StockWaste::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'document_number' => 'SW-POSTED-' . time(),
            'date' => now()->subDays(2)->toDateString(),
            'status' => 'draft',
            'notes' => 'Pembuangan Susu Kedaluwarsa (Telah Diposting)',
            'created_by' => 1
        ]);

        foreach ($products as $product) {
            StockWasteItem::create([
                'stock_waste_id' => $wastePosted->id,
                'product_id' => $product->id,
                'qty' => 5,
                'reason' => 'Expired',
                'cost' => $product->cost ?? 15000
            ]);
        }

        // Jalankan alur workflow posting untuk wastePosted
        $wasteService->submitDocument($wastePosted->uuid);
        $wasteService->approveDocument($wastePosted->uuid);
        $wasteService->postDocument($wastePosted->uuid);
    }
}
