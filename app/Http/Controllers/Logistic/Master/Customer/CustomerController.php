<?php

namespace App\Http\Controllers\Logistic\Master\Customer;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Customer;
use App\Http\Requests\MasterData\CustomerRequest;
use App\Services\MasterData\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return view('pages.logistic.master.customer.index');
    }

    public function create()
    {
        return view('pages.logistic.master.customer.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $customers = Customer::with(['currency', 'tax', 'accountReceivable'])
            ->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'uuid', 'code', 'name', 'phone', 'email', 'status'])
            ->latest();

        return DataTables::of($customers)
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(CustomerRequest $request)
    {
        $this->customerService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $customer = Customer::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.customer.partials.show_modal', compact('customer'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $customer = Customer::where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.customer.partials.edit_modal', compact('customer'));
    }

    public function update(CustomerRequest $request, $uuid)
    {
        $this->customerService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->customerService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus.'
        ]);
    }
}
