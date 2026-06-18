<?php

namespace App\Http\Controllers\Logistic\Master\Supplier;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Supplier;
use App\Http\Requests\MasterData\SupplierRequest;
use App\Services\MasterData\SupplierService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        return view('pages.logistic.master.supplier.index');
    }

    public function create()
    {
        return view('pages.logistic.master.supplier.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $suppliers = Supplier::where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'phone', 'email', 'contact_person_name', 'city', 'status'])
            ->latest();

        return DataTables::of($suppliers)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(SupplierRequest $request)
    {
        $this->supplierService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pemasok baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $supplier = Supplier::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.supplier.partials.show_modal', compact('supplier'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $supplier = Supplier::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.supplier.partials.edit_modal', compact('supplier'));
    }

    public function update(SupplierRequest $request, $uuid)
    {
        $this->supplierService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data pemasok berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->supplierService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Pemasok berhasil dihapus.'
        ]);
    }
}
