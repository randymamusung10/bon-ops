<?php

namespace App\Http\Controllers\Logistic\Inventory\StockAdjustment;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Inventory\StockAdjustment;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;
use App\Services\Logistic\Inventory\StockAdjustmentService;
use App\Http\Requests\Logistic\Inventory\StockAdjustmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentController extends Controller
{
    protected $service;

    public function __construct(StockAdjustmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.logistic.inventory.stock_adjustment.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $adjustments = StockAdjustment::with(['branch', 'warehouse', 'creator', 'poster'])
            ->where('tenant_id', $tenantId)
            ->latest();

        return DataTables::of($adjustments)
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.inventory.stock_adjustment.partials.create_modal', compact('branches', 'warehouses', 'products'));
    }

    public function store(StockAdjustmentRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Penyesuaian Stok berhasil dibuat (Draft).'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $adjustment = StockAdjustment::with(['branch', 'warehouse', 'items.product', 'creator', 'poster'])
            ->where('uuid', $uuid)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('pages.logistic.inventory.stock_adjustment.partials.show_modal', compact('adjustment'));
    }

    public function post($uuid)
    {
        $this->service->post($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Penyesuaian Stok berhasil di-posting.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->service->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Penyesuaian Stok berhasil dihapus.'
        ]);
    }
}
