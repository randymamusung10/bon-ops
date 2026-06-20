<?php

namespace App\Http\Controllers\Logistic\Inventory\StockOpname;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Services\Logistic\Inventory\StockOpnameService;
use App\Repositories\Logistic\Inventory\StockOpnameRepository;
use App\Http\Requests\Logistic\Inventory\StockOpnameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(StockOpnameService $service, StockOpnameRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        return view('pages.logistic.inventory.opname.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $opnames = $this->repository->getBaseQuery($tenantId)->latest();

        return DataTables::of($opnames)
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

        return view('pages.logistic.inventory.opname.partials.create_modal', compact('branches', 'warehouses', 'products'));
    }

    public function store(StockOpnameRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil dibuat (Draft).'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $opname = $this->repository->findByUuid($tenantId, $uuid);

        return view('pages.logistic.inventory.opname.partials.show_modal', compact('opname'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $opname = $this->repository->findByUuid($tenantId, $uuid);

        if ($opname->status !== 'draft') {
            abort(403, 'Hanya dokumen draft yang dapat diedit.');
        }

        $branches = Branch::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();
        $products = Product::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.inventory.opname.partials.edit_modal', compact('opname', 'branches', 'warehouses', 'products'));
    }

    public function update(StockOpnameRequest $request, $uuid)
    {
        $this->service->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil diperbarui.'
        ]);
    }

    public function submit($uuid)
    {
        $this->service->submitDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil diajukan.'
        ]);
    }

    public function approve($uuid)
    {
        $this->service->approveDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil disetujui.'
        ]);
    }

    public function post($uuid)
    {
        $this->service->postDocument($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil di-posting (stok telah diperbarui).'
        ]);
    }

    public function destroy($uuid)
    {
        $this->service->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen Stock Opname berhasil dihapus.'
        ]);
    }

    public function getSystemStock(Request $request)
    {
        $warehouseId = $request->query('warehouse_id');
        $tenantId = Auth::user()->tenant_id ?? 1;

        $products = Product::with('unit')->where('tenant_id', $tenantId)->get();
        $balances = InventoryBalance::where('tenant_id', $tenantId)
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->keyBy('product_id');

        $data = $products->map(function ($product) use ($balances) {
            $balance = $balances->get($product->id);
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_name' => $product->unit->name ?? 'pcs',
                'system_qty' => $balance ? (float)$balance->qty : 0.0,
            ];
        });

        return response()->json($data);
    }
}
