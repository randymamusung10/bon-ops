<?php

namespace App\Services\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\SupplierInvoiceItem;
use App\Repositories\Logistic\Purchasing\SupplierInvoiceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierInvoiceService
{
    protected $repository;

    public function __construct(SupplierInvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createDraft(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            // Internal Document Number
            $docNo = 'INV-' . date('YmdHis') . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);

            $invoice = $this->repository->create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'supplier_id' => $data['supplier_id'],
                'goods_receipt_id' => $data['goods_receipt_id'] ?? null,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'document_number' => $docNo,
                'supplier_invoice_number' => $data['supplier_invoice_number'],
                'date' => $data['date'],
                'due_date' => $data['due_date'],
                'subtotal' => $data['subtotal'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                SupplierInvoiceItem::create([
                    'supplier_invoice_id' => $invoice->id,
                    'goods_receipt_item_id' => $item['goods_receipt_item_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $invoice;
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
            
            $invoice = $this->repository->findByUuid($tenantId, $uuid);

            if ($invoice->status !== 'draft') {
                throw new Exception("Hanya faktur draft yang dapat disubmit.");
            }

            $this->repository->updateStatus($invoice, 'submitted');

            DB::commit();
            return $invoice;
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
            
            $invoice = $this->repository->findByUuid($tenantId, $uuid);

            if ($invoice->status !== 'submitted') {
                throw new Exception("Hanya faktur submitted yang dapat diapprove.");
            }

            $this->repository->updateStatus($invoice, 'approved', [
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            DB::commit();
            return $invoice;
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

            $invoice = $this->repository->findByUuid($tenantId, $uuid);

            if ($invoice->status !== 'approved') {
                throw new Exception("Hanya faktur approved yang dapat diposting.");
            }

            // Di sini nanti bisa ditambahkan integrasi ke modul Keuangan/Account Payable
            // untuk mengakui liability hutang secara resmi di jurnal umum.

            $this->repository->updateStatus($invoice, 'posted', [
                'posted_by' => Auth::id(),
                'posted_at' => now()
            ]);

            DB::commit();
            return $invoice;
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

            $invoice = $this->repository->findByUuid($tenantId, $uuid);

            if ($invoice->status !== 'draft') {
                throw new Exception("Hanya faktur dengan status draft yang dapat dihapus.");
            }

            $this->repository->delete($invoice);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
