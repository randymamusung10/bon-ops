<?php

namespace App\Http\Controllers\Logistic\Inventory; // Wait! Is it App\Http\Controllers\Logistic\Inventory\StockWaste or App\Http\Controllers\Logistic\Inventory?
// Let's check namespace of the existing StockWasteController first:
// The placeholder was: namespace App\Http\Controllers\Logistic\Inventory\StockWaste;
// Let's keep it exact:
namespace App\Http\Controllers\Logistic\Inventory\StockWaste;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;
use App\Services\Logistic\Inventory\StockWasteService;
use App\Repositories\Logistic\Inventory\StockWasteRepository;
use App\Http\Requests\Logistic\Inventory\StockWasteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockWasteController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(StockWasteService $service, StockWasteRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.logistic.inventory.waste.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $wastes = $this->repository->getBaseQuery($tenantId)->latest();

        return DataTables::of($wastes)
            ->editColumn('date', function ($model) {
                return \Carbon\Carbon::parse($model->date)->format('d/m/Y');
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
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
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $branches = Branch::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.inventory.waste.partials.create_modal', compact('branches', 'warehouses', 'products'));
    }

    public function store(StockWasteRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil dibuat (Draft).'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $waste = $this->repository->findByUuid($tenantId, $uuid);

        return view('pages.logistic.inventory.waste.partials.show_modal', compact('waste'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $waste = $this->repository->findByUuid($tenantId, $uuid);

        if ($waste->status !== 'draft') {
            abort(403, 'Hanya dokumen draft yang dapat diedit.');
        }

        $branches = Branch::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.inventory.waste.partials.edit_modal', compact('waste', 'branches', 'warehouses', 'products'));
    }

    public function update(StockWasteRequest $request, $uuid)
    {
        $this->service->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil diperbarui.'
        ]);
    }

    public function submit($uuid)
    {
        $this->service->submitDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil diajukan.'
        ]);
    }

    public function approve($uuid)
    {
        $this->service->approveDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil disetujui.'
        ]);
    }

    public function post($uuid)
    {
        $this->service->postDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil di-posting (stok telah diperbarui).'
        ]);
    }

    public function destroy($uuid)
    {
        $this->service->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Waste berhasil dihapus.'
        ]);
    }
}
