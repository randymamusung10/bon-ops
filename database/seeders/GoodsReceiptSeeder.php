<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Purchasing\PurchaseOrder;
use App\Services\Logistic\Purchasing\GoodsReceiptService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoodsReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GoodsReceiptService $service): void
    {
        // Login as Superadmin to satisfy Auth checks in Service
        $user = User::first();
        if ($user) {
            Auth::login($user);
        }

        $branch = Branch::first();
        $warehouse = Warehouse::where('branch_id', $branch->id)->first() ?? Warehouse::first();
        
        // Cari PO yang berstatus posted
        $postedPos = PurchaseOrder::with('items')->where('status', 'posted')->get();

        if (!$branch || !$warehouse || $postedPos->isEmpty()) {
            $this->command->info('Please run Purchase Order seeder first (and ensure there is a posted PO).');
            return;
        }

        // 1. GR Posted (Penerimaan Full)
        if (isset($postedPos[0])) {
            $po1 = $postedPos[0];
            $items1 = [];
            foreach ($po1->items as $item) {
                $items1[] = [
                    'purchase_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id,
                    'ordered_qty' => $item->quantity,
                    'received_qty' => $item->quantity, // Terima full
                    'notes' => 'Diterima lengkap',
                ];
            }

            $this->command->info('Creating Posted GR (Penerimaan Full)...');
            $gr1 = $service->createDraft([
                'branch_id' => $po1->branch_id,
                'warehouse_id' => $warehouse->id,
                'purchase_order_id' => $po1->id,
                'supplier_id' => $po1->supplier_id,
                'date' => now()->toDateString(),
                'notes' => 'Penerimaan barang dari PO ' . $po1->document_number,
                'items' => $items1,
            ]);
            $service->submitDocument($gr1->uuid);
            $service->approveDocument($gr1->uuid);
            $service->postDocument($gr1->uuid);
        }

        // 2. GR Draft (Penerimaan Sebagian)
        if (isset($postedPos[1])) {
            $po2 = $postedPos[1];
            $items2 = [];
            foreach ($po2->items as $item) {
                $items2[] = [
                    'purchase_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id,
                    'ordered_qty' => $item->quantity,
                    'received_qty' => max(0, $item->quantity - 5), // Terima kurang dari pesanan
                    'notes' => 'Barang kurang',
                ];
            }

            $this->command->info('Creating Draft GR (Penerimaan Sebagian)...');
            $service->createDraft([
                'branch_id' => $po2->branch_id,
                'warehouse_id' => $warehouse->id,
                'purchase_order_id' => $po2->id,
                'supplier_id' => $po2->supplier_id,
                'date' => now()->toDateString(),
                'notes' => 'Penerimaan parsial barang dari PO ' . $po2->document_number,
                'items' => $items2,
            ]);
        }

        $this->command->info('Goods Receipt seeders completed successfully.');
    }
}
