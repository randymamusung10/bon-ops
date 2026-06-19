<?php

namespace App\Http\Controllers\Logistic\Master\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Logistic\Master\Company\CompanyService;
use App\Http\Requests\Logistic\Master\Company\CompanyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        return view('pages.logistic.master.company.index');
    }

    public function create()
    {
        return view('pages.logistic.master.company.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $companies = Company::where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'uuid', 'name', 'status'])
            ->latest();

        return DataTables::of($companies)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(CompanyRequest $request)
    {
        $this->companyService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $company = Company::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.company.partials.show_modal', compact('company'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $company = Company::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.company.partials.edit_modal', compact('company'));
    }

    public function update(CompanyRequest $request, $uuid)
    {
        $this->companyService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->companyService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil dihapus.'
        ]);
    }

    public function select2(Request $request)
    {
        $results = $this->companyService->getForSelect2($request->q);

        return response()->json([
            'results' => $results
        ]);
    }
}
