<?php

namespace App\Http\Controllers\Business\Reports\StockReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logistic\Inventory\InventoryBalance;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class StockReportController extends Controller
{
    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $warehouses = \App\Models\Logistic\Master\Warehouse\Warehouse::where('tenant_id', $tenantId)->get();
        return view('pages.business.reports.stock.index', compact('warehouses'));
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = InventoryBalance::with(['product', 'warehouse'])->where('tenant_id', $tenantId);

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '-';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '-';
            })
            ->addColumn('warehouse_name', function ($row) {
                return $row->warehouse ? $row->warehouse->name : '-';
            })
            ->editColumn('qty', function ($row) {
                return number_format($row->qty, 2, ',', '.');
            })
            ->addColumn('cost', function ($row) {
                return $row->product ? number_format($row->product->cost, 2, ',', '.') : '0,00';
            })
            ->addColumn('valuation', function ($row) {
                $cost = $row->product ? $row->product->cost : 0;
                $val = $row->qty * $cost;
                return number_format($val, 2, ',', '.');
            })
            ->make(true);
    }
}
