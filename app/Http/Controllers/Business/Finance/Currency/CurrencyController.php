<?php

namespace App\Http\Controllers\Business\Finance\Currency;

use App\Http\Controllers\Controller;
use App\Models\Business\Finance\Currency\Currency;
use App\Http\Requests\Business\Finance\Currency\CurrencyRequest;
use App\Services\Business\Finance\Currency\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        return view('pages.business.finance.currency.index');
    }

    public function create()
    {
        return view('pages.business.finance.currency.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $currencies = Currency::where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'symbol', 'exchange_rate', 'status'])
            ->latest();

        return DataTables::of($currencies)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(CurrencyRequest $request)
    {
        $this->currencyService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mata Uang baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $currency = Currency::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.currency.partials.show_modal', compact('currency'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $currency = Currency::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.business.finance.currency.partials.edit_modal', compact('currency'));
    }

    public function update(CurrencyRequest $request, $uuid)
    {
        $this->currencyService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data Mata Uang berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->currencyService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Mata Uang berhasil dihapus.'
        ]);
    }
}
