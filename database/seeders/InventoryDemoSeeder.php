<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use App\Models\Logistic\Master\Unit\Unit;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Inventory\StockAdjustment;
use App\Models\Logistic\Inventory\StockAdjustmentItem;
use App\Services\Logistic\Inventory\StockAdjustmentService;
use Illuminate\Support\Str;

class InventoryDemoSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\Auth::loginUsingId(1);

        // Get or create tenant
        $tenant = Tenant::firstOrCreate(['id' => 1], ['name' => 'Default Tenant']);
        $tenantId = $tenant->id;

        // 1. Create Company
        $company = \App\Models\Company::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'PT Mitra Logistik'],
            ['status' => 'active']
        );

        // 2. Create Branches
        $branchA = Branch::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-01'],
            ['name' => 'Cabang Jakarta Pusat', 'company_id' => $company->id, 'status' => 'active']
        );

        $branchB = Branch::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-02'],
            ['name' => 'Cabang Bandung', 'company_id' => $company->id, 'status' => 'active']
        );

        // 3. Create Warehouses
        $warehouseA = Warehouse::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'WH-JKT-01'],
            ['branch_id' => $branchA->id, 'name' => 'Gudang Utama Jakarta', 'company_id' => $company->id, 'status' => 'active']
        );

        $warehouseB = Warehouse::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'WH-BDG-01'],
            ['branch_id' => $branchB->id, 'name' => 'Gudang Utama Bandung', 'company_id' => $company->id, 'status' => 'active']
        );

        // 4. Create Units & Categories
        $unitPcs = Unit::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'PCS'], ['name' => 'Pieces', 'company_id' => $company->id, 'status' => 'active']);
        $unitKg = Unit::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'KG'], ['name' => 'Kilogram', 'company_id' => $company->id, 'status' => 'active']);
        $unitBox = Unit::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'BOX'], ['name' => 'Box', 'company_id' => $company->id, 'status' => 'active']);

        $catFood = ProductCategory::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'FOOD'], ['name' => 'Makanan', 'company_id' => $company->id, 'status' => 'active']);
        $catBev = ProductCategory::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'BEV'], ['name' => 'Minuman', 'company_id' => $company->id, 'status' => 'active']);
        $catIng = ProductCategory::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'ING'], ['name' => 'Bahan Baku', 'company_id' => $company->id, 'status' => 'active']);

        // 5. Create Products
        $prod1 = Product::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'PRD-001'], [
            'name' => 'Biji Kopi Arabica', 'product_category_id' => $catIng->id, 'unit_id' => $unitKg->id, 'company_id' => $company->id, 'status' => 'active', 'type' => 'goods'
        ]);
        $prod2 = Product::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'PRD-002'], [
            'name' => 'Susu UHT 1L', 'product_category_id' => $catIng->id, 'unit_id' => $unitBox->id, 'company_id' => $company->id, 'status' => 'active', 'type' => 'goods'
        ]);
        $prod3 = Product::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'PRD-003'], [
            'name' => 'Gula Pasir', 'product_category_id' => $catIng->id, 'unit_id' => $unitKg->id, 'company_id' => $company->id, 'status' => 'active', 'type' => 'goods'
        ]);
        $prod4 = Product::firstOrCreate(['tenant_id' => $tenantId, 'code' => 'PRD-004'], [
            'name' => 'Syrup Vanilla', 'product_category_id' => $catIng->id, 'unit_id' => $unitPcs->id, 'company_id' => $company->id, 'status' => 'active', 'type' => 'goods'
        ]);

        // 6. Create Draft Adjustment
        $draftAdj = StockAdjustment::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchA->id,
            'warehouse_id' => $warehouseA->id,
            'document_number' => 'ADJ-DRAFT-' . time(),
            'date' => now()->toDateString(),
            'status' => 'draft',
            'notes' => 'Draft stok opname akhir bulan',
            'created_by' => 1
        ]);
        StockAdjustmentItem::create(['stock_adjustment_id' => $draftAdj->id, 'product_id' => $prod1->id, 'system_qty' => 0, 'actual_qty' => 50.5, 'difference' => 50.5, 'reason' => 'Stok awal']);
        StockAdjustmentItem::create(['stock_adjustment_id' => $draftAdj->id, 'product_id' => $prod2->id, 'system_qty' => 0, 'actual_qty' => 120, 'difference' => 120, 'reason' => 'Stok awal']);

        // 7. Create & Post Adjustments to simulate movements
        $adjService = new StockAdjustmentService();
        
        // JKT Posted Adjustment
        $adj1 = StockAdjustment::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchA->id,
            'warehouse_id' => $warehouseA->id,
            'document_number' => 'ADJ-JKT-' . time(),
            'date' => now()->subDays(2)->toDateString(),
            'status' => 'draft',
            'notes' => 'Stok awal setup sistem (Jakarta)',
            'created_by' => 1
        ]);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj1->id, 'product_id' => $prod1->id, 'system_qty' => 0, 'actual_qty' => 100, 'difference' => 100, 'reason' => 'Stok opname awal']);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj1->id, 'product_id' => $prod2->id, 'system_qty' => 0, 'actual_qty' => 500, 'difference' => 500, 'reason' => 'Stok opname awal']);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj1->id, 'product_id' => $prod3->id, 'system_qty' => 0, 'actual_qty' => 300, 'difference' => 300, 'reason' => 'Stok opname awal']);
        $adjService->post($adj1->uuid);

        // BDG Posted Adjustment
        $adj2 = StockAdjustment::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchB->id,
            'warehouse_id' => $warehouseB->id,
            'document_number' => 'ADJ-BDG-' . time(),
            'date' => now()->subDays(1)->toDateString(),
            'status' => 'draft',
            'notes' => 'Stok awal setup sistem (Bandung)',
            'created_by' => 1
        ]);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj2->id, 'product_id' => $prod1->id, 'system_qty' => 0, 'actual_qty' => 50, 'difference' => 50, 'reason' => 'Stok opname awal']);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj2->id, 'product_id' => $prod4->id, 'system_qty' => 0, 'actual_qty' => 100, 'difference' => 100, 'reason' => 'Stok opname awal']);
        $adjService->post($adj2->uuid);

        // JKT Correction (Koreksi stok)
        $adj3 = StockAdjustment::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchA->id,
            'warehouse_id' => $warehouseA->id,
            'document_number' => 'ADJ-KOR-' . time(),
            'date' => now()->toDateString(),
            'status' => 'draft',
            'notes' => 'Koreksi karena susu bocor',
            'created_by' => 1
        ]);
        StockAdjustmentItem::create(['stock_adjustment_id' => $adj3->id, 'product_id' => $prod2->id, 'system_qty' => 500, 'actual_qty' => 495, 'difference' => -5, 'reason' => 'Bocor di gudang']);
        $adjService->post($adj3->uuid);

    }
}
