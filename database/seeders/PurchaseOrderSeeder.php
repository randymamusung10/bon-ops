<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Supplier\Supplier;
use App\Models\Logistic\Master\Product\Product;
use App\Services\Logistic\Purchasing\PurchaseOrderService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PurchaseOrderService $service): void
    {
        // Login as Superadmin to satisfy Auth checks in Service
        $user = User::first();
        if ($user) {
            Auth::login($user);
        }

        $branch = Branch::first();
        $supplier = Supplier::first();
        $products = Product::with('unit')->take(3)->get();

        if (!$branch || !$supplier || $products->isEmpty()) {
            $this->command->info('Please run master data seeders first (Branch, Supplier, Product, Unit).');
            return;
        }

        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'product_id' => $product->id,
                'unit_id' => $product->unit_id,
                'quantity' => rand(10, 50),
                'unit_price' => rand(10000, 50000),
            ];
        }

        // 1. PO Draft
        $this->command->info('Creating Draft PO...');
        $service->createDraft([
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'date' => now()->toDateString(),
            'expected_date' => now()->addDays(3)->toDateString(),
            'notes' => 'PO Draft dari seeder',
            'items' => $items,
        ]);

        // 2. PO Submitted
        $this->command->info('Creating Submitted PO...');
        $poSubmitted = $service->createDraft([
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'date' => now()->subDays(1)->toDateString(),
            'expected_date' => now()->addDays(2)->toDateString(),
            'notes' => 'PO Submitted dari seeder',
            'items' => $items,
        ]);
        $service->submitDocument($poSubmitted->uuid);

        // 3. PO Approved
        $this->command->info('Creating Approved PO...');
        $poApproved = $service->createDraft([
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'date' => now()->subDays(2)->toDateString(),
            'expected_date' => now()->addDays(1)->toDateString(),
            'notes' => 'PO Approved dari seeder',
            'items' => $items,
        ]);
        $service->submitDocument($poApproved->uuid);
        $service->approveDocument($poApproved->uuid);

        // 4. PO Posted
        $this->command->info('Creating Posted PO...');
        $poPosted = $service->createDraft([
            'branch_id' => $branch->id,
            'supplier_id' => $supplier->id,
            'date' => now()->subDays(3)->toDateString(),
            'expected_date' => now()->toDateString(),
            'notes' => 'PO Posted dari seeder',
            'items' => $items,
        ]);
        $service->submitDocument($poPosted->uuid);
        $service->approveDocument($poPosted->uuid);
        $service->postDocument($poPosted->uuid);

        $this->command->info('Purchase Order seeders completed successfully.');
    }
}
