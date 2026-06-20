<?php

namespace App\Http\Controllers\Logistic\Master\Recipe;

use App\Http\Controllers\Controller;
use App\Services\Logistic\Master\Recipe\RecipeService;
use App\Http\Requests\Logistic\Master\Recipe\RecipeRequest;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;
use App\Services\Logistic\Master\ProductionStation\ProductionStationService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    protected $service;
    protected $stationService;

    public function __construct(RecipeService $service, ProductionStationService $stationService)
    {
        $this->service = $service;
        $this->stationService = $stationService;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        
        // Products that can have recipes (finished_good or semi_finished_good)
        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['finished_good', 'semi_finished_good', 'menu'])
            ->where('status', 'active')
            ->get();

        // Ingredients (raw_material, semi_finished_good, etc.)
        $ingredients = Product::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $stations = $this->stationService->getActive();
        $units = Unit::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.master.recipe.index', compact('products', 'ingredients', 'stations', 'units'));
    }

    public function create()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['finished_good', 'semi_finished_good', 'menu'])
            ->where('status', 'active')
            ->get();
        $ingredients = Product::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();
        $stations = $this->stationService->getActive();
        $units = Unit::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.master.recipe.partials.create_modal', compact('products', 'ingredients', 'stations', 'units'));
    }

    public function data()
    {
        $recipes = $this->service->getAll();

        return DataTables::of($recipes)
            ->addColumn('total_cost', function ($row) {
                $total = $row->items->sum('cost');
                return number_format($total, 2, ',', '.');
            })
            ->make(true);
    }

    public function store(RecipeRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Resep baru berhasil disimpan.'
        ]);
    }

    public function show($uuid)
    {
        $recipe = $this->service->findByUuid($uuid);
        return view('pages.logistic.master.recipe.partials.show_modal', compact('recipe'));
    }

    public function edit($uuid)
    {
        $recipe = $this->service->findByUuid($uuid);
        $tenantId = Auth::user()->tenant_id ?? 1;

        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['finished_good', 'semi_finished_good', 'menu'])
            ->where('status', 'active')
            ->get();

        $ingredients = Product::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $stations = $this->stationService->getActive();
        $units = Unit::where('tenant_id', $tenantId)->get();

        return view('pages.logistic.master.recipe.partials.edit_modal', compact('recipe', 'products', 'ingredients', 'stations', 'units'));
    }

    public function update(RecipeRequest $request, $uuid)
    {
        $this->service->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data resep berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->service->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil dihapus.'
        ]);
    }
}
