<?php

namespace App\Http\Controllers\Logistic\Inventory\StockTransfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Logistic\Inventory\StockTransferRequest;
use App\Services\Logistic\Inventory\StockTransferService;
use App\Repositories\Logistic\Inventory\StockTransferRepository;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;

class StockTransferController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(StockTransferService $service, StockTransferRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.logistic.inventory.transfer.index');
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.inventory.transfer.partials.create_modal', compact('branches', 'warehouses', 'products'));
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
            ->addColumn('source', function($row) {
                return $row->sourceBranch->name . ' - ' . $row->sourceWarehouse->name;
            })
            ->addColumn('destination', function($row) {
                return $row->destinationBranch->name . ' - ' . $row->destinationWarehouse->name;
            })
            ->addColumn('status_badge', function($row) {
                $statusMap = [
                    'draft' => '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Draft</span>',
                    'submitted' => '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Submitted</span>',
                    'approved' => '<span class="badge bg-info-subtle text-info px-2 py-1 rounded-pill">Approved</span>',
                    'posted' => '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Posted</span>',
                ];
                return $statusMap[$row->status] ?? $row->status;
            })
            ->addColumn('action', function($row) {
                return '<button type="button" class="btn btn-sm btn-light border btn-show" data-uuid="'.$row->uuid.'"><i class="bi bi-eye"></i> Detail</button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(StockTransferRequest $request)
    {
        try {
            $this->service->createDraft($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Mutasi stok berhasil disimpan sebagai Draft.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($uuid)
    {
        try {
            $this->service->delete($uuid);
            return response()->json([
                'success' => true,
                'message' => 'Dokumen Mutasi Stok berhasil dihapus.'
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
        $transfer = $this->repository->findByUuid($tenantId, $uuid);
        
        return view('pages.logistic.inventory.transfer.partials.show_modal', compact('transfer'));
    }

    public function submit($uuid)
    {
        try {
            $this->service->submitDocument($uuid);
            return response()->json(['message' => 'Mutasi stok berhasil diajukan untuk persetujuan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approve($uuid)
    {
        try {
            $this->service->approveDocument($uuid);
            return response()->json(['message' => 'Mutasi stok berhasil disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function post($uuid)
    {
        try {
            $this->service->postDocument($uuid);
            return response()->json(['message' => 'Mutasi stok berhasil diposting. Stok telah diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
