<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Product\Product;
use App\Services\Logistic\Purchasing\PurchaseRequestService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PurchaseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PurchaseRequestService $service): void
    {
        // Login as Superadmin to satisfy Auth checks in Service
        $user = User::first();
        if ($user) {
            Auth::login($user);
        }

        $branch = Branch::first();
        $products = Product::with('unit')->take(3)->get();

        if (!$branch || $products->isEmpty()) {
            $this->command->info('Please run master data seeders first (Branch, Product, Unit).');
            return;
        }

        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'product_id' => $product->id,
                'unit_id' => $product->unit_id,
                'quantity' => rand(5, 20),
                'notes' => 'Permintaan barang ' . $product->name,
            ];
        }

        // 1. PR Draft
        $this->command->info('Creating Draft PR...');
        $service->createDraft([
            'branch_id' => $branch->id,
            'date' => now()->toDateString(),
            'expected_date' => now()->addDays(5)->toDateString(),
            'notes' => 'PR Draft dari seeder untuk kebutuhan stok bulanan',
            'items' => $items,
        ]);

        // 2. PR Submitted
        $this->command->info('Creating Submitted PR...');
        $prSubmitted = $service->createDraft([
            'branch_id' => $branch->id,
            'date' => now()->subDays(1)->toDateString(),
            'expected_date' => now()->addDays(4)->toDateString(),
            'notes' => 'PR Submitted dari seeder (Menunggu Approval)',
            'items' => $items,
        ]);
        $service->submitDocument($prSubmitted->uuid);

        // 3. PR Approved
        $this->command->info('Creating Approved PR...');
        $prApproved = $service->createDraft([
            'branch_id' => $branch->id,
            'date' => now()->subDays(2)->toDateString(),
            'expected_date' => now()->addDays(3)->toDateString(),
            'notes' => 'PR Approved dari seeder (Siap untuk dibuat PO)',
            'items' => $items,
        ]);
        $service->submitDocument($prApproved->uuid);
        $service->approveDocument($prApproved->uuid);

        // 4. PR Posted
        $this->command->info('Creating Posted PR...');
        $prPosted = $service->createDraft([
            'branch_id' => $branch->id,
            'date' => now()->subDays(3)->toDateString(),
            'expected_date' => now()->addDays(2)->toDateString(),
            'notes' => 'PR Posted dari seeder',
            'items' => $items,
        ]);
        $service->submitDocument($prPosted->uuid);
        $service->approveDocument($prPosted->uuid);
        $service->postDocument($prPosted->uuid);

        $this->command->info('Purchase Request seeders completed successfully.');
    }
}
