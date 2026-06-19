<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Purchasing\GoodsReceipt;
use App\Models\User;
use App\Services\Logistic\Purchasing\SupplierInvoiceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Login sebagai user pertama agar Auth::id() tersedia di CLI context
        $adminUser = User::first();
        if (!$adminUser) {
            $this->command->error('Tidak ada User. Jalankan UserSeeder terlebih dahulu.');
            return;
        }
        Auth::login($adminUser);

        $service = app(SupplierInvoiceService::class);

        // Find a posted GR
        $gr = GoodsReceipt::with('items.purchaseOrderItem')->where('status', 'posted')->first();

        if (!$gr) {
            $this->command->info('Tidak ada Goods Receipt yang posted. Silakan jalankan GoodsReceiptSeeder terlebih dahulu.');
            return;
        }

        $items = [];
        $subtotal = 0;
        foreach ($gr->items as $item) {
            $unitPrice = $item->purchaseOrderItem->unit_price ?? 0;
            $totalPrice = $item->received_qty * $unitPrice;
            $subtotal += $totalPrice;

            $items[] = [
                'goods_receipt_item_id' => $item->id,
                'product_id' => $item->product_id,
                'unit_id' => $item->unit_id,
                'quantity' => $item->received_qty,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => 'Ditagihkan penuh sesuai GR'
            ];
        }

        $taxAmount = $subtotal * 0.11; // PPN 11%
        $discountAmount = 0;
        $grandTotal = $subtotal + $taxAmount - $discountAmount;

        DB::beginTransaction();
        try {
            // Create Invoice (Draft)
            $invoice = $service->createDraft([
                'branch_id' => $gr->branch_id,
                'supplier_id' => $gr->supplier_id,
                'goods_receipt_id' => $gr->id,
                'purchase_order_id' => $gr->purchase_order_id,
                'supplier_invoice_number' => 'INV-SPL-' . date('Ymd') . '-01',
                'date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'notes' => 'Tagihan otomatis dari seeder',
                'items' => $items
            ]);

            // Submit, Approve, Post
            $service->submitDocument($invoice->uuid);
            $service->approveDocument($invoice->uuid);
            $service->postDocument($invoice->uuid);

            DB::commit();
            $this->command->info('Supplier Invoice berhasil dibuat dan diposting.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
