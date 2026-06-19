<?php

namespace App\Http\Controllers\Logistic\Purchasing\GoodsReceipt;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Purchasing\PurchaseOrder;
use App\Repositories\Logistic\Purchasing\GoodsReceiptRepository;
use App\Services\Logistic\Purchasing\GoodsReceiptService;
use App\Http\Requests\Logistic\Purchasing\GoodsReceiptRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class GoodsReceiptController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(GoodsReceiptRepository $repository, GoodsReceiptService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.logistic.purchasing.receipt.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('warehouse_name', function ($row) {
                return $row->warehouse->name ?? '-';
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier->name ?? '-';
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
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        // Fetch only POs that are posted and have not been fully received
        $purchaseOrders = PurchaseOrder::where('tenant_id', $tenantId)
            ->where('status', 'posted')
            ->get();

        return view('pages.logistic.purchasing.receipt.partials.create_modal', compact('warehouses', 'purchaseOrders'));
    }

    // AJAX endpoint to get PO details and items
    public function getPoDetails($id)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $po = PurchaseOrder::with(['items.product', 'items.unit'])->where('tenant_id', $tenantId)->findOrFail($id);
        
        return response()->json([
            'branch_id' => $po->branch_id,
            'supplier_id' => $po->supplier_id,
            'items' => $po->items
        ]);
    }

    public function store(GoodsReceiptRequest $request)
    {
        try {
            // Get PO to append branch_id and supplier_id
            $po = PurchaseOrder::findOrFail($request->purchase_order_id);
            
            $data = $request->validated();
            $data['branch_id'] = $po->branch_id;
            $data['supplier_id'] = $po->supplier_id;

            $this->service->createDraft($data);
            return response()->json(['success' => true, 'message' => 'Penerimaan Barang berhasil disimpan (Draft).']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $receipt = $this->repository->findByUuid($tenantId, $uuid);
        return view('pages.logistic.purchasing.receipt.partials.show_modal', compact('receipt'));
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Barang berhasil disubmit.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Barang berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Penerimaan Barang berhasil diposting. Stok Gudang bertambah.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($uuid)
    {
        try {
            $this->service->deleteDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Dokumen Penerimaan Barang berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
