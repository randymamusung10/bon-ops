<?php

namespace App\Http\Controllers\Logistic\Inventory\StockBalance;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Inventory\InventoryBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockBalanceController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.stock_balance.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $balances = InventoryBalance::with(['branch', 'warehouse', 'product.unit'])
            ->where('tenant_id', $tenantId);

        if ($request->warehouse_id) {
            $balances->where('warehouse_id', $request->warehouse_id);
        }

        return DataTables::of($balances)
            ->make(true);
    }
}
