<?php

namespace App\Http\Controllers\Logistic\Master\Branch;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Branch\Branch;
use App\Services\Logistic\Master\Branch\BranchService;
use App\Http\Requests\Logistic\Master\Branch\BranchRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    public function index()
    {
        return view('pages.logistic.master.branch.index');
    }

    public function create()
    {
        return view('pages.logistic.master.branch.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $branches = Branch::with('company')->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'city', 'address', 'status'])
            ->latest();

        return DataTables::of($branches)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(BranchRequest $request)
    {
        $this->branchService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cabang baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branch = Branch::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.branch.partials.show_modal', compact('branch'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branch = Branch::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.branch.partials.edit_modal', compact('branch'));
    }

    public function update(BranchRequest $request, $uuid)
    {
        $this->branchService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data cabang berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->branchService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil dihapus.'
        ]);
    }

    public function select2(Request $request)
    {
        $results = $this->branchService->getForSelect2($request->q);

        return response()->json([
            'results' => $results
        ]);
    }
}
