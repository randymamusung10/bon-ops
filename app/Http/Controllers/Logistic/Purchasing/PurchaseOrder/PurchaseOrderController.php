<?php

namespace App\Http\Controllers\Logistic\Purchasing\PurchaseOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Logistic\Purchasing\PurchaseOrderRequest;
use App\Services\Logistic\Purchasing\PurchaseOrderService;
use App\Repositories\Logistic\Purchasing\PurchaseOrderRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Supplier\Supplier;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;
use App\Models\Logistic\Purchasing\PurchaseRequest;

class PurchaseOrderController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(PurchaseOrderService $service, PurchaseOrderRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.logistic.purchasing.order.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->getBaseQuery($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->editColumn('total_amount', function ($model) {
                return 'Rp ' . number_format($model->total_amount, 2, ',', '.');
            })
            ->addColumn('supplier_name', function($row) {
                return $row->supplier ? $row->supplier->name : '-';
            })
            ->addColumn('branch_name', function($row) {
                return $row->branch ? $row->branch->name : '-';
            })
            ->addColumn('status_badge', function($row) {
                $statusMap = [
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>',
                    'submitted' => '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>',
                    'approved' => '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>',
                    'posted' => '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted</span>',
                    'closed' => '<span class="badge bg-dark-subtle text-dark px-2 py-1 rounded-pill">Closed</span>',
                ];
                return $statusMap[$row->status] ?? $row->status;
            })
            ->addColumn('action', function($row) {
                return '<button type="button" class="btn btn-sm btn-light border btn-show" data-uuid="'.$row->uuid.'"><i class="bi bi-eye"></i> Detail</button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();
        $units = Unit::where('tenant_id', $tenantId)->get();
        $purchaseRequests = PurchaseRequest::where('tenant_id', $tenantId)
            ->whereIn('status', ['approved', 'posted'])
            ->get();

        return view('pages.logistic.purchasing.order.partials.create_modal', compact('branches', 'suppliers', 'products', 'units', 'purchaseRequests'));
    }

    public function store(PurchaseOrderRequest $request)
    {
        try {
            $this->service->createDraft($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order berhasil disimpan sebagai Draft.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPurchaseRequestDetails($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $pr = PurchaseRequest::with(['items.product', 'items.unit'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pr->id,
                'branch_id' => $pr->branch_id,
                'items' => $pr->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'unit_id' => $item->unit_id,
                        'unit_name' => $item->unit->name,
                        'quantity' => $item->quantity,
                    ];
                })
            ]
        ]);
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = $this->repository->findByUuid($tenantId, $uuid);
        
        if ($order->status !== 'draft') {
            return response()->json(['message' => 'Hanya dokumen Draft yang dapat diedit.'], 403);
        }

        $branches = Branch::where('tenant_id', $tenantId)->get();
        $suppliers = Supplier::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();
        $units = Unit::where('tenant_id', $tenantId)->get();
        $purchaseRequests = PurchaseRequest::where('tenant_id', $tenantId)
            ->whereIn('status', ['approved', 'posted'])
            ->get();

        return view('pages.logistic.purchasing.order.partials.edit_modal', compact('order', 'branches', 'suppliers', 'products', 'units', 'purchaseRequests'));
    }

    public function update(PurchaseOrderRequest $request, $uuid)
    {
        try {
            $this->service->updateDraft($uuid, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = $this->repository->findByUuid($tenantId, $uuid);
        
        return view('pages.logistic.purchasing.order.partials.show_modal', compact('order'));
    }

    public function destroy($uuid)
    {
        try {
            $this->service->delete($uuid);
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order Draft berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['message' => 'Purchase Order berhasil diajukan untuk persetujuan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['message' => 'Purchase Order berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['message' => 'Purchase Order berhasil diposting. Siap dikirim ke Supplier.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
