<?php

namespace App\Http\Controllers\Logistic\Inventory\StockCard;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Inventory\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockCardController extends Controller
{
    public function index()
    {
        return view('pages.logistic.inventory.stock_card.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $movements = InventoryMovement::with(['branch', 'warehouse', 'product.unit'])
            ->where('tenant_id', $tenantId);

        if ($request->warehouse_id) {
            $movements->where('warehouse_id', $request->warehouse_id);
        }
        
        if ($request->product_id) {
            $movements->where('product_id', $request->product_id);
        }

        $movements->orderBy('date', 'desc')->orderBy('id', 'desc');

        return DataTables::of($movements)
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->make(true);
    }
}
