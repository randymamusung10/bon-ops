<?php

namespace App\Http\Controllers\Business\Reports\ItemizedSalesReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operational\Pos\PosOrderItem;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class ItemizedSalesReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.sales_itemized.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = PosOrderItem::with(['posOrder.creator', 'product.unit'])
            ->whereHas('posOrder', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('posOrder', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->whereHas('posOrder', function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row->posOrder ? \Carbon\Carbon::parse($row->posOrder->date)->format('d/m/Y') : '-';
            })
            ->addColumn('order_number', function ($row) {
                return $row->posOrder ? $row->posOrder->order_number : '-';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '-';
            })
            ->editColumn('qty', function ($row) {
                $unit = $row->product && $row->product->unit ? ' ' . $row->product->unit->code : '';
                return number_format($row->qty, 2, ',', '.') . $unit;
            })
            ->editColumn('price', function ($row) {
                return number_format($row->price, 2, ',', '.');
            })
            ->editColumn('subtotal', function ($row) {
                return number_format($row->subtotal, 2, ',', '.');
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        $filename = "itemized_sales_report_" . date('Ymd_His') . ".xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ItemizedSalesReportExport($request->all()), $filename);
    }
}
