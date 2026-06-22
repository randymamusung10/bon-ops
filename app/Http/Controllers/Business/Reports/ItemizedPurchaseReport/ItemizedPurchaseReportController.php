<?php

namespace App\Http\Controllers\Business\Reports\ItemizedPurchaseReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logistic\Purchasing\SupplierInvoiceItem;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class ItemizedPurchaseReportController extends Controller
{
    public function index()
    {
        return view('pages.business.reports.purchase_itemized.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = SupplierInvoiceItem::with(['supplierInvoice.supplier', 'product', 'unit'])
            ->whereHas('supplierInvoice', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('supplierInvoice', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->whereHas('supplierInvoice', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row->supplierInvoice ? \Carbon\Carbon::parse($row->supplierInvoice->date)->format('d/m/Y') : '-';
            })
            ->addColumn('invoice_number', function ($row) {
                return $row->supplierInvoice ? $row->supplierInvoice->supplier_invoice_number : '-';
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplierInvoice && $row->supplierInvoice->supplier ? $row->supplierInvoice->supplier->name : '-';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '-';
            })
            ->editColumn('quantity', function ($row) {
                $unit = $row->unit ? ' ' . $row->unit->code : '';
                return number_format($row->quantity, 2, ',', '.') . $unit;
            })
            ->editColumn('unit_price', function ($row) {
                return number_format($row->unit_price, 2, ',', '.');
            })
            ->editColumn('total_price', function ($row) {
                return number_format($row->total_price, 2, ',', '.');
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        $filename = "itemized_purchase_report_" . date('Ymd_His') . ".xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ItemizedPurchaseReportExport($request->all()), $filename);
    }
}
