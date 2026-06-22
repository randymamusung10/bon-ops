<?php

namespace App\Http\Controllers\Business\Finance\AccountsPayable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Logistic\Purchasing\SupplierPaymentService;

class AccountsPayableController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.payable.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        // Fetch supplier invoices that are posted and not fully paid
        $query = SupplierInvoice::with('supplier')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['posted', 'paid']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier->name ?? '-';
            })
            ->editColumn('supplier_invoice_number', function ($row) {
                return $row->supplier_invoice_number;
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->editColumn('due_date', function ($row) {
                return \Carbon\Carbon::parse($row->due_date)->format('d/m/Y');
            })
            ->editColumn('grand_total', function ($row) {
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('remaining_balance', function ($row) {
                if ($row->status === 'paid') {
                    return number_format(0, 2, ',', '.');
                }
                
                // Calculate from SupplierPayment (include all active to avoid overpayment)
                $totalActivePaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $row->id)
                    ->whereIn('status', ['draft', 'submitted', 'approved', 'posted'])
                    ->sum('payment_amount');
                
                $remaining = max(0, $row->grand_total - $totalActivePaid);
                return number_format($remaining, 2, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'paid') {
                    return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>';
                }
                
                $totalActivePaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $row->id)
                    ->whereIn('status', ['draft', 'submitted', 'approved', 'posted'])
                    ->sum('payment_amount');
                
                if ($totalActivePaid > 0 && $totalActivePaid < $row->grand_total) {
                    return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Dibayar Sebagian (atau Dalam Proses)</span>';
                }
                
                if ($totalActivePaid >= $row->grand_total) {
                    return '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Menunggu Approval</span>';
                }
                
                return '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Belum Dibayar</span>';
            })
            ->addColumn('action', function ($row) {
                $btnShow = '<button class="btn-icon-modern text-info btn-show" data-uuid="'.$row->uuid.'" title="Detail" style="background: rgba(14, 165, 233, 0.12);"><i class="bi bi-eye"></i></button>';
                if ($row->status === 'paid') {
                    return '<div class="d-inline-flex gap-2">'.$btnShow.'</div>';
                }
                $btnPay = '<button class="btn-icon-modern text-success btn-pay" data-uuid="'.$row->uuid.'" title="Bayar Hutang" style="background: rgba(16, 185, 129, 0.12);"><i class="bi bi-check2-circle"></i></button>';
                return '<div class="d-inline-flex gap-2">'.$btnShow.$btnPay.'</div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function showModal($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $invoice = SupplierInvoice::with(['supplier', 'purchaseOrder', 'items.product', 'payments' => function($q) {
            $q->where('status', 'posted');
        }])->where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        
        return view('pages.business.finance.payable.partials.show_modal', compact('invoice'));
    }

    public function paymentModal($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $invoice = SupplierInvoice::with(['supplier', 'payments' => function($q) {
            $q->whereIn('status', ['draft', 'submitted', 'approved', 'posted']);
        }])->where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        
        $totalPosted = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $invoice->id)
            ->where('status', 'posted')
            ->sum('payment_amount');
            
        $totalPending = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $invoice->id)
            ->whereIn('status', ['draft', 'submitted', 'approved'])
            ->sum('payment_amount');
            
        $invoice->remaining_amount = max(0, $invoice->grand_total - ($totalPosted + $totalPending));
        
        return view('pages.business.finance.payable.partials.payment_modal', compact('invoice', 'uuid', 'totalPending'));
    }

    public function pay(Request $request, $uuid, SupplierPaymentService $paymentService)
    {
        try {
            $request->validate([
                'payment_date'        => 'required|date',
                'payment_method'      => 'required|string',
                'payment_amount'      => 'required|numeric|min:0.01',
                'bank_reference'      => 'nullable|string|max:100',
                'bank_name'           => 'nullable|string|max:100',
                'bank_account_number' => 'nullable|string|max:100',
                'notes'               => 'nullable|string',
                'attachment'          => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
            ]);

            $tenantId = Auth::user()->tenant_id ?? 1;
            $invoice = SupplierInvoice::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();

            $data = $request->all();
            $data['supplier_invoice_id'] = $invoice->id;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payments/supplier', $filename, 'public');
                $data['attachment_path'] = 'storage/payments/supplier/' . $filename;
            }
            
            // Remove dots from payment amount
            if (isset($data['payment_amount'])) {
                $data['payment_amount'] = (float) str_replace('.', '', $data['payment_amount']);
            }
            
            // Create draft payment
            $payment = $paymentService->createDraft($data);
            
            // Process it fully (submit, approve, post) to make it consistent with Accounts Receivable seamless experience
            $paymentService->submitDocument($payment->uuid);
            $paymentService->approveDocument($payment->uuid);
            $paymentService->postDocument($payment->uuid);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran hutang berhasil dicatat dan diposting.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
