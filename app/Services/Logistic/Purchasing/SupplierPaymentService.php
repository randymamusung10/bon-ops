<?php

namespace App\Services\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\SupplierInvoice;
use App\Repositories\Logistic\Purchasing\SupplierPaymentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierPaymentService
{
    protected $repository;

    public function __construct(SupplierPaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $docNo = 'PAY-' . date('YmdHis') . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);

            // Fetch the invoice to get amount reference
            $invoice = SupplierInvoice::findOrFail($data['supplier_invoice_id']);

            // Validasi overpayment
            $totalActivePayments = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $invoice->id)
                ->whereIn('status', ['draft', 'submitted', 'approved', 'posted'])
                ->sum('payment_amount');

            $remainingAmount = max(0, $invoice->grand_total - $totalActivePayments);
            
            $paymentAmount = \App\Helpers\NumberHelper::parse($data['payment_amount'] ?? 0);

            if ($paymentAmount > $remainingAmount) {
                throw new Exception("Nominal pembayaran melebihi sisa tagihan. Terdapat kemungkinan pembayaran ganda atau antrean pembayaran (Sisa yang dapat dibayar: Rp " . number_format($remainingAmount, 2, ',', '.') . ").");
            }

            $payment = $this->repository->create([
                'tenant_id'              => $tenantId,
                'branch_id'              => $invoice->branch_id,
                'supplier_id'            => $invoice->supplier_id,
                'supplier_invoice_id'    => $invoice->id,
                'document_number'        => $docNo,
                'payment_date'           => $data['payment_date'],
                'payment_method'         => $data['payment_method'],
                'bank_name'              => $data['bank_name'] ?? null,
                'bank_account_number'    => $data['bank_account_number'] ?? null,
                'bank_reference'         => $data['bank_reference'] ?? null,
                'payment_amount'         => $data['payment_amount'],
                'invoice_amount'         => $invoice->grand_total,
                'status'                 => 'draft',
                'notes'                  => $data['notes'] ?? null,
                'attachment_path'        => $data['attachment_path'] ?? null,
                'created_by'             => Auth::id(),
            ]);

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function submitDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $payment = $this->repository->findByUuid($tenantId, $uuid);

            if ($payment->status !== 'draft') {
                throw new Exception("Hanya pembayaran draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($payment, 'submitted');
            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $payment = $this->repository->findByUuid($tenantId, $uuid);

            if ($payment->status !== 'submitted') {
                throw new Exception("Hanya pembayaran submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($payment, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function postDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $payment = $this->repository->findByUuid($tenantId, $uuid);

            if ($payment->status !== 'approved') {
                throw new Exception("Hanya pembayaran approved yang dapat diposting.");
            }

            $this->repository->updateStatus($payment, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
            ]);

            // Tandai Invoice sebagai PAID jika total pembayaran >= grand_total
            $invoice = $payment->supplierInvoice;
            if ($invoice) {
                $totalPaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $invoice->id)
                    ->where('status', 'posted')
                    ->sum('payment_amount');

                if ($totalPaid >= $invoice->grand_total) {
                    $invoice->status = 'paid';
                    $invoice->save();
                }
            }

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDraft(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $payment = $this->repository->findByUuid($tenantId, $uuid);

            if ($payment->status !== 'draft') {
                throw new Exception("Hanya pembayaran draft yang dapat diperbarui.");
            }

            $invoice = SupplierInvoice::findOrFail($data['supplier_invoice_id']);

            // Validasi overpayment (exclude current payment uuid)
            $totalActivePayments = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $invoice->id)
                ->where('uuid', '!=', $uuid)
                ->whereIn('status', ['draft', 'submitted', 'approved', 'posted'])
                ->sum('payment_amount');

            $remainingAmount = max(0, $invoice->grand_total - $totalActivePayments);
            
            $paymentAmount = \App\Helpers\NumberHelper::parse($data['payment_amount'] ?? 0);

            if ($paymentAmount > $remainingAmount) {
                throw new Exception("Nominal pembayaran melebihi sisa tagihan. Terdapat kemungkinan pembayaran ganda atau antrean pembayaran (Sisa yang dapat dibayar: Rp " . number_format($remainingAmount, 2, ',', '.') . ").");
            }

            $payment->update([
                'supplier_invoice_id' => $invoice->id,
                'branch_id'           => $invoice->branch_id,
                'supplier_id'         => $invoice->supplier_id,
                'payment_date'        => $data['payment_date'],
                'payment_method'      => $data['payment_method'],
                'bank_name'           => $data['bank_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_reference'      => $data['bank_reference'] ?? null,
                'payment_amount'      => \App\Helpers\NumberHelper::parse($data['payment_amount']),
                'invoice_amount'      => $invoice->grand_total,
                'notes'               => $data['notes'] ?? null,
                'attachment_path'     => $data['attachment_path'] ?? $payment->attachment_path,
            ]);

            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteDocument(string $uuid)
    {
        try {
            DB::beginTransaction();
            $tenantId = Auth::user()->tenant_id ?? 1;

            $payment = $this->repository->findByUuid($tenantId, $uuid);

            if ($payment->status !== 'draft') {
                throw new Exception("Hanya pembayaran draft yang dapat dihapus.");
            }

            $this->repository->delete($payment);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
