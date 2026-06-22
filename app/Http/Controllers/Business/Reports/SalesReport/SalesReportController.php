<?php

namespace App\Http\Controllers\Business\Reports\SalesReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operational\Pos\PosOrder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SalesReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.sales.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = PosOrder::with('creator')->where('tenant_id', $tenantId);

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->editColumn('grand_total', function ($row) {
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('cashier_name', function ($row) {
                return $row->creator ? $row->creator->name : '-';
            })
            ->addColumn('payment_status_badge', function ($row) {
                switch($row->payment_status) {
                    case 'paid':
                        return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>';
                    case 'partial':
                        return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Parsial</span>';
                    case 'unpaid':
                        return '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Belum Bayar</span>';
                    case 'refunded':
                        return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Refund</span>';
                    default:
                        return '<span class="badge bg-light text-dark px-2 py-1 rounded-pill">Unknown</span>';
                }
            })
            ->rawColumns(['payment_status_badge'])
            ->make(true);
    }
}
