<?php

namespace App\Http\Controllers\Business\Finance\ChartOfAccount;

use App\Http\Controllers\Controller;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use App\Http\Requests\Business\Finance\ChartOfAccount\ChartOfAccountRequest;
use App\Services\Business\Finance\ChartOfAccount\ChartOfAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    public function index()
    {
        return view('pages.business.finance.coa.index');
    }

    public function create()
    {
        return view('pages.business.finance.coa.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $coas = ChartOfAccount::with('parent')->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'account_type', 'is_header', 'parent_id', 'status'])
            ->latest();

        return DataTables::of($coas)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->filterColumn('account_type', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('account_type', $keyword);
                }
            })
            ->addColumn('parent_name', function ($coa) {
                return $coa->parent ? '[' . $coa->parent->code . '] ' . $coa->parent->name : '-';
            })
            ->make(true);
    }

    public function store(ChartOfAccountRequest $request)
    {
        $this->coaService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Akun baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $coa = ChartOfAccount::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.coa.partials.show_modal', compact('coa'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $coa = ChartOfAccount::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.coa.partials.edit_modal', compact('coa'));
    }

    public function update(ChartOfAccountRequest $request, $uuid)
    {
        $this->coaService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Akun berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->coaService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus.'
        ]);
    }

    public function select2(Request $request)
    {
        $onlyDetail = $request->has('only_detail') ? filter_var($request->only_detail, FILTER_VALIDATE_BOOLEAN) : false;
        $onlyHeader = $request->has('only_header') ? filter_var($request->only_header, FILTER_VALIDATE_BOOLEAN) : false;

        $results = $this->coaService->getForSelect2($request->q, $request->type, $onlyDetail, $onlyHeader);

        return response()->json([
            'results' => $results
        ]);
    }
}
