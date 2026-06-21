<?php

namespace App\Http\Controllers\Business\Finance\AccountsPayable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                
                // Calculate from SupplierPayment
                $totalPaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $row->id)
                    ->where('status', 'posted')
                    ->sum('payment_amount');
                
                $remaining = max(0, $row->grand_total - $totalPaid);
                return number_format($remaining, 2, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'paid') {
                    return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>';
                }
                
                $totalPaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $row->id)
                    ->where('status', 'posted')
                    ->sum('payment_amount');
                
                if ($totalPaid > 0 && $totalPaid < $row->grand_total) {
                    return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Dibayar Sebagian</span>';
                }
                
                return '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Belum Dibayar</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="d-inline-flex gap-2">
                            <button class="btn-icon-modern text-info btn-show" data-uuid="'.$row->uuid.'" title="Detail" style="background: rgba(14, 165, 233, 0.12);"><i class="bi bi-eye"></i></button>
                        </div>';
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
}
