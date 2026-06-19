<?php

namespace App\Http\Controllers\Logistic\Purchasing\PurchaseRequest;

use App\Http\Controllers\Controller;
use App\Services\Logistic\Purchasing\PurchaseRequestService;
use App\Repositories\Logistic\Purchasing\PurchaseRequestRepository;
use App\Http\Requests\Logistic\Purchasing\PurchaseRequestRequest;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(PurchaseRequestService $service, PurchaseRequestRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.logistic.purchasing.request.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('branch_name', function ($row) {
                return $row->branch->name ?? '-';
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                if ($status === 'draft') return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>';
                if ($status === 'submitted') return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>';
                if ($status === 'approved') return '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>';
                if ($status === 'posted') return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted</span>';
                if ($status === 'closed') return '<span class="badge bg-dark-subtle text-dark px-2 py-1 rounded-pill">Closed</span>';
                return '<span class="badge bg-dark">'.$status.'</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();
        $units = Unit::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.purchasing.request.partials.create_modal', compact('branches', 'products', 'units'));
    }

    public function store(PurchaseRequestRequest $request)
    {
        try {
            $this->service->createDraft($request->validated());
            return response()->json(['success' => true, 'message' => 'Draft Purchase Request berhasil dibuat.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $requestData = $this->repository->findByUuid($tenantId, $uuid);
        return view('pages.logistic.purchasing.request.partials.show_modal', ['requestData' => $requestData]);
    }

    public function destroy($uuid)
    {
        try {
            $this->service->delete($uuid);
            return response()->json(['success' => true, 'message' => 'Draft PR berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Dokumen PR berhasil disubmit.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Dokumen PR berhasil diapprove.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function postDoc($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Dokumen PR berhasil diposting.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
