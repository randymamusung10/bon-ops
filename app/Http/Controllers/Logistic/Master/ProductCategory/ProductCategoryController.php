<?php

namespace App\Http\Controllers\Logistic\Master\ProductCategory;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use App\Http\Requests\Logistic\Master\ProductCategory\ProductCategoryRequest;
use App\Services\Logistic\Master\ProductCategory\ProductCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(ProductCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return view('pages.logistic.master.product-category.index');
    }

    public function create()
    {
        return view('pages.logistic.master.product-category.partials.create_modal');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        $categories = ProductCategory::with('parent')->where('tenant_id', $tenantId)
            ->select(['id', 'tenant_id', 'company_id', 'parent_id', 'uuid', 'code', 'name', 'status'])
            ->latest();

        return DataTables::of($categories)
            ->addColumn('parent_name', function($category) {
                return $category->parent ? $category->parent->name : '-';
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword !== '') {
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }

    public function store(ProductCategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori produk baru berhasil ditambahkan.'
        ]);
    }

    public function show($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $category = ProductCategory::with('parent')->where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.product-category.partials.show_modal', compact('category'));
    }

    public function edit($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $category = ProductCategory::with('parent')->where('uuid', $uuid)->where('tenant_id', $tenantId)->firstOrFail();

        return view('pages.logistic.master.product-category.partials.edit_modal', compact('category'));
    }

    public function update(ProductCategoryRequest $request, $uuid)
    {
        $this->categoryService->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->categoryService->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Kategori produk berhasil dihapus.'
        ]);
    }
    
    public function select2(Request $request)
    {
        $results = $this->categoryService->getForSelect2($request->q, $request->exclude_uuid);

        return response()->json([
            'results' => $results
        ]);
    }
}
