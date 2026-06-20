<?php

namespace App\Http\Controllers\Logistic\Master\ProductionStation;

use App\Http\Controllers\Controller;
use App\Services\Logistic\Master\ProductionStation\ProductionStationService;
use App\Http\Requests\Logistic\Master\ProductionStation\ProductionStationRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductionStationController extends Controller
{
    protected $service;

    public function __construct(ProductionStationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.logistic.master.station.index');
    }

    public function create()
    {
        return view('pages.logistic.master.station.partials.create_modal');
    }

    public function data()
    {
        $stations = $this->service->getAll();

        return DataTables::of($stations)
            ->make(true);
    }

    public function store(ProductionStationRequest $request)
    {
        $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Stasiun produksi baru berhasil ditambahkan.'
        ]);
    }

    public function edit($uuid)
    {
        $station = $this->service->findByUuid($uuid);
        return view('pages.logistic.master.station.partials.edit_modal', compact('station'));
    }

    public function update(ProductionStationRequest $request, $uuid)
    {
        $this->service->update($uuid, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data stasiun produksi berhasil diperbarui.'
        ]);
    }

    public function destroy($uuid)
    {
        $this->service->delete($uuid);

        return response()->json([
            'success' => true,
            'message' => 'Stasiun produksi berhasil dihapus.'
        ]);
    }

    public function select2(Request $request)
    {
        $stations = $this->service->getActive();
        
        $results = $stations->map(function ($station) {
            return [
                'id' => $station->id,
                'text' => $station->name . ' (' . $station->code . ')'
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }
}
