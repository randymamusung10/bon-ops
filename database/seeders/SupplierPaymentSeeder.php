<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use App\Models\User;
use App\Services\Logistic\Purchasing\SupplierPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::first();
        if (!$adminUser) {
            $this->command->error('Tidak ada User. Jalankan UserSeeder terlebih dahulu.');
            return;
        }
        Auth::login($adminUser);

        $service = app(SupplierPaymentService::class);

        // Ambil Invoice yang sudah di-posted
        $invoice = SupplierInvoice::where('status', 'posted')->first();

        if (!$invoice) {
            $this->command->info('Tidak ada Faktur dengan status Posted. Jalankan SupplierInvoiceSeeder terlebih dahulu.');
            return;
        }

        DB::beginTransaction();
        try {
            $payment = $service->createDraft([
                'supplier_invoice_id' => $invoice->id,
                'payment_date'        => now()->toDateString(),
                'payment_method'      => 'transfer',
                'bank_name'           => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_reference'      => 'TRF-' . date('YmdHis'),
                'payment_amount'      => $invoice->grand_total,
                'notes'               => 'Pembayaran lunas sesuai Faktur ' . $invoice->document_number,
            ]);

            $service->submitDocument($payment->uuid);
            $service->approveDocument($payment->uuid);
            $service->postDocument($payment->uuid);

            DB::commit();
            $this->command->info('Pembayaran Supplier berhasil dibuat dan diposting. Invoice ' . $invoice->document_number . ' telah dilunasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
