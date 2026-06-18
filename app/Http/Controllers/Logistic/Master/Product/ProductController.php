<?php

namespace App\Http\Controllers\Logistic\Master\Product;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use App\Models\Logistic\Master\Unit\Unit;
use App\Models\Business\Finance\Tax\Tax;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use App\Http\Requests\Logistic\Master\Product\ProductRequest;
use App\Services\Logistic\Master\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return view('pages.logistic.master.product.index');
    }

    public function create()
    {
        // For simple selects we could pass them, but we use select2 AJAX for category, unit, coa, tax.
        return view('pages.logistic.master.product.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $products = Product::with(['category', 'unit'])->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'product_category_id', 'unit_id', 'uuid', 'code', 'name', 'type', 'price', 'status'])
            ->latest();

        return DataTables::of($products)
            ->addColumn('category_name', function($product) {
                return $product->category ? $product->category->name : '-';
            })
            ->addColumn('unit_name', function($product) {
                return $product->unit ? $product->unit->name : '-';
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(ProductRequest $request)
    {
        $this->productService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $product = Product::with(['category', 'unit', 'tax', 'inventoryAccount', 'cogsAccount', 'incomeAccount', 'priceHistories.creator'])
            ->where('uuid', $uuid)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('pages.logistic.master.product.partials.show_modal', compact('product'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $product = Product::with(['category', 'unit', 'tax', 'inventoryAccount', 'cogsAccount', 'incomeAccount'])
            ->where('uuid', $uuid)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('pages.logistic.master.product.partials.edit_modal', compact('product'));
    }

    public function update(ProductRequest $request, $uuid)
    {
        $this->productService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->productService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.'
        ]);
    }
}
