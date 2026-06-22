<?php

namespace App\Http\Controllers\Business\Reports\PurchaseReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PurchaseReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.purchase.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = SupplierInvoice::with('supplier')->where('tenant_id', $tenantId);

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->editColumn('supplier_invoice_number', function ($row) {
                return $row->supplier_invoice_number;
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier ? $row->supplier->name : '-';
            })
            ->editColumn('grand_total', function ($row) {
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                switch($row->status) {
                    case 'paid':
                        return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>';
                    case 'posted':
                        return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Belum Lunas (Posted)</span>';
                    case 'draft':
                    case 'submitted':
                    case 'approved':
                        return '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill text-capitalize">'.$row->status.'</span>';
                    default:
                        return '<span class="badge bg-light text-dark px-2 py-1 rounded-pill text-capitalize">'.$row->status.'</span>';
                }
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }
}
