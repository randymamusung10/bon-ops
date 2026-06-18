<?php

namespace App\Http\Controllers\Business\Finance\Tax;

use App\Http\Controllers\Controller;
use App\Models\Business\Finance\Tax\Tax;
use App\Http\Requests\Business\Finance\Tax\TaxRequest;
use App\Services\Business\Finance\Tax\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TaxController extends Controller
{
    protected $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    public function index()
    {
        return view('pages.business.finance.tax.index');
    }

    public function create()
    {
        return view('pages.business.finance.tax.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $taxes = Tax::where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'rate_percentage', 'status'])
            ->latest();

        return DataTables::of($taxes)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(TaxRequest $request)
    {
        $this->taxService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pajak baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $tax = Tax::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.tax.partials.show_modal', compact('tax'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $tax = Tax::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.tax.partials.edit_modal', compact('tax'));
    }

    public function update(TaxRequest $request, $uuid)
    {
        $this->taxService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Pajak berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->taxService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Pajak berhasil dihapus.'
        ]);
    }

    public function select2(Request $request)
    {
        $results = $this->taxService->getForSelect2($request->q);

        return response()->json([
            'results' => $results
        ]);
    }
}
