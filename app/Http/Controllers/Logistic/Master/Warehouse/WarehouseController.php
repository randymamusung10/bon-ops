<?php

namespace App\Http\Controllers\Logistic\Master\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Branch\Branch;
use App\Http\Requests\Logistic\Master\Warehouse\WarehouseRequest;
use App\Services\Logistic\Master\Warehouse\WarehouseService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        return view('pages.logistic.master.warehouse.index');
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->where('status', 'active')->get();
        return view('pages.logistic.master.warehouse.partials.create_modal', compact('branches'));
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $warehouses = Warehouse::with('branch')->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'branch_id', 'uuid', 'code', 'name', 'city', 'status'])
            ->latest();

        return DataTables::of($warehouses)
            ->addColumn('branch_name', function($warehouse) {
                return $warehouse->branch ? $warehouse->branch->name : '-';
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(WarehouseRequest $request)
    {
        $this->warehouseService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Gudang baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $warehouse = Warehouse::with('branch')->where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.warehouse.partials.show_modal', compact('warehouse'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $warehouse = Warehouse::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();
        $branches = Branch::where('tenant_id', $tenantId)->where('status', 'active')->get();

        return view('pages.logistic.master.warehouse.partials.edit_modal', compact('warehouse', 'branches'));
    }

    public function update(WarehouseRequest $request, $uuid)
    {
        $this->warehouseService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data gudang berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->warehouseService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Gudang berhasil dihapus.'
        ]);
    }
}
