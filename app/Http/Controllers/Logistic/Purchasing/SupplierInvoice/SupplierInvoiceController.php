<?php

namespace App\Http\Controllers\Logistic\Purchasing\SupplierInvoice;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Purchasing\GoodsReceipt;
use App\Models\Logistic\Master\Supplier\Supplier;
use App\Repositories\Logistic\Purchasing\SupplierInvoiceRepository;
use App\Services\Logistic\Purchasing\SupplierInvoiceService;
use App\Http\Requests\Logistic\Purchasing\SupplierInvoiceRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SupplierInvoiceController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(SupplierInvoiceRepository $repository, SupplierInvoiceService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.logistic.purchasing.invoice.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier->name ?? '-';
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->editColumn('due_date', function ($row) {
                return \Carbon\Carbon::parse($row->due_date)->format('d/m/Y');
            })
            ->editColumn('grand_total', function ($row) {
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('remaining_balance', function ($row) {
                if (in_array($row->status, ['draft', 'submitted', 'approved'])) {
                    return number_format($row->grand_total, 2, ',', '.');
                }
                if ($row->status === 'paid') {
                    return number_format(0, 2, ',', '.');
                }
                
                $totalPaid = \App\Models\Logistic\Purchasing\SupplierPayment::where('supplier_invoice_id', $row->id)
                    ->where('status', 'posted')
                    ->sum('payment_amount');
                $remaining = max(0, $row->grand_total - $totalPaid);
                return number_format($remaining, 2, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                if ($status === 'draft') return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>';
                if ($status === 'submitted') return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>';
                if ($status === 'approved') return '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>';
                if ($status === 'posted') return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted / AP</span>';
                if ($status === 'paid') return '<span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill">Lunas</span>';
                if ($status === 'closed') return '<span class="badge bg-dark-subtle text-dark px-2 py-1 rounded-pill">Closed</span>';
                return '<span class="badge bg-dark">'.$status.'</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        // Fetch GRs that are posted, to be invoiced
        // Idealnya: whereNotIn(goods_receipt_id, supplier_invoices->whereNotNull) agar tidak dobel tagihan.
        $goodsReceipts = GoodsReceipt::with('supplier')
            ->where('tenant_id', $tenantId)
            ->where('status', 'posted')
            ->get();

        return view('pages.logistic.purchasing.invoice.partials.create_modal', compact('goodsReceipts'));
    }

    // AJAX endpoint to get GR details and prices from PO
    public function getGrDetails($id)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $gr = GoodsReceipt::with([
            'items.product', 
            'items.unit',
            'items.purchaseOrderItem'
        ])->where('tenant_id', $tenantId)->findOrFail($id);
        
        $items = $gr->items->map(function($item) {
            $unitPrice = $item->purchaseOrderItem->unit_price ?? 0;
            return [
                'goods_receipt_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'unit_id' => $item->unit_id,
                'unit_name' => $item->unit->name,
                'received_qty' => $item->received_qty,
                'unit_price' => $unitPrice,
                'total_price' => $item->received_qty * $unitPrice
            ];
        });

        return response()->json([
            'branch_id' => $gr->branch_id,
            'supplier_id' => $gr->supplier_id,
            'purchase_order_id' => $gr->purchase_order_id,
            'supplier_name' => $gr->supplier->name ?? '-',
            'items' => $items
        ]);
    }

    public function store(SupplierInvoiceRequest $request)
    {
        try {
            $gr = GoodsReceipt::findOrFail($request->goods_receipt_id);
            
            $data = $request->validated();
            $data['branch_id'] = $gr->branch_id;
            $data['supplier_id'] = $gr->supplier_id;
            $data['purchase_order_id'] = $gr->purchase_order_id;

            $this->service->createDraft($data);
            return response()->json(['success' => true, 'message' => 'Faktur Supplier berhasil disimpan (Draft).']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $invoice = $this->repository->findByUuid($tenantId, $uuid);
        return view('pages.logistic.purchasing.invoice.partials.show_modal', compact('invoice'));
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Faktur berhasil disubmit.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Faktur berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Faktur berhasil diposting. Menjadi Hutang (AP) yang sah.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $invoice = $this->repository->findByUuid($tenantId, $uuid);

        if ($invoice->status !== 'draft') {
            abort(403, "Hanya faktur draft yang dapat diedit.");
        }

        return view('pages.logistic.purchasing.invoice.partials.edit_modal', compact('invoice'));
    }

    public function update(SupplierInvoiceRequest $request, $uuid)
    {
        try {
            $this->service->updateDraft($uuid, $request->validated());
            return response()->json(['success' => true, 'message' => 'Faktur Supplier berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($uuid)
    {
        try {
            $this->service->deleteDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Faktur berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
