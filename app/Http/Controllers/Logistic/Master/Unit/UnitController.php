<?php

namespace App\Http\Controllers\Logistic\Master\Unit;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Unit\Unit;
use App\Http\Requests\Logistic\Master\Unit\UnitRequest;
use App\Services\Logistic\Master\Unit\UnitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        return view('pages.logistic.master.unit.index');
    }

    public function create()
    {
        return view('pages.logistic.master.unit.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $units = Unit::where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'description', 'status'])
            ->latest();

        return DataTables::of($units)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(UnitRequest $request)
    {
        $this->unitService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Satuan baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $unit = Unit::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.unit.partials.show_modal', compact('unit'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $unit = Unit::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.unit.partials.edit_modal', compact('unit'));
    }

    public function update(UnitRequest $request, $uuid)
    {
        $this->unitService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data satuan berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->unitService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil dihapus.'
        ]);
    }
    
    public function select2(Request $request)
    {
        $results = $this->unitService->getForSelect2($request->q);

        return response()->json([
            'results' => $results
        ]);
    }
}
