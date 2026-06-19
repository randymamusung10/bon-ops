<?php

namespace App\Http\Controllers\Logistic\Purchasing\SupplierPayment;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use App\Repositories\Logistic\Purchasing\SupplierPaymentRepository;
use App\Services\Logistic\Purchasing\SupplierPaymentService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SupplierPaymentController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(SupplierPaymentRepository $repository, SupplierPaymentService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.logistic.purchasing.payment.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = $this->repository->datatable($tenantId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('supplier_name', fn($row) => $row->supplier->name ?? '-')
            ->addColumn('invoice_number', fn($row) => $row->supplierInvoice->document_number ?? '-')
            ->editColumn('payment_date', fn($row) => \Carbon\Carbon::parse($row->payment_date)->format('d/m/Y'))
            ->editColumn('payment_amount', fn($row) => number_format($row->payment_amount, 2, ',', '.'))
            ->addColumn('status_badge', function($row) {
                $statusMap = [
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>',
                    'submitted' => '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>',
                    'approved' => '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>',
                    'posted' => '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted / Lunas</span>',
                    'closed' => '<span class="badge bg-dark-subtle text-dark px-2 py-1 rounded-pill">Closed</span>',
                ];
                return $statusMap[$row->status] ?? '<span class="badge bg-dark">'.$row->status.'</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        // Only posted invoices not yet fully paid
        $invoices = SupplierInvoice::with('supplier')
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['posted'])
            ->get();

        return view('pages.logistic.purchasing.payment.partials.create_modal', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_invoice_id' => 'required|exists:supplier_invoices,id',
            'payment_date'        => 'required|date',
            'payment_method'      => 'required|string',
            'payment_amount'      => 'required|numeric|min:0.01',
            'bank_reference'      => 'nullable|string|max:100',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        try {
            $this->service->createDraft($request->all());
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil disimpan sebagai Draft.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $payment = $this->repository->findByUuid($tenantId, $uuid);
        return view('pages.logistic.purchasing.payment.partials.show_modal', compact('payment'));
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil disubmit.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Pembayaran berhasil diposting. Hutang AP pada Faktur telah dilunasi.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($uuid)
    {
        try {
            $this->service->deleteDocument($uuid);
            return response()->json(['success' => true, 'message' => 'Draft Pembayaran berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
