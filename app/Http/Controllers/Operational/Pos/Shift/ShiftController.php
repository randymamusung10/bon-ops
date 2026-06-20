<?php

namespace App\Http\Controllers\Operational\Pos\Shift;

use App\Http\Controllers\Controller;
use App\Services\Operational\Pos\PosShiftService;
use App\Repositories\Operational\Pos\PosShiftRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(PosShiftService $service, PosShiftRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $userId = Auth::id();
        $activeShift = $this->repository->getActiveShift($tenantId, $userId);

        return view('pages.operational.pos.shift.index', compact('activeShift'));
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $shifts = $this->repository->getBaseQuery($tenantId)->latest();

        return DataTables::of($shifts)
            ->editColumn('start_time', function ($model) {
                return $model->start_time ? $model->start_time->format('d/m/Y H:i') : '-';
            })
            ->editColumn('end_time', function ($model) {
                return $model->end_time ? $model->end_time->format('d/m/Y H:i') : '-';
            })
            ->editColumn('start_cash', function ($model) {
                return 'Rp ' . number_format($model->start_cash, 0, ',', '.');
            })
            ->editColumn('actual_end_cash', function ($model) {
                return $model->actual_end_cash !== null ? 'Rp ' . number_format($model->actual_end_cash, 0, ',', '.') : '-';
            })
            ->addColumn('status_badge', function($row) {
                if ($row->status === 'open') {
                    return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Open</span>';
                }
                return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Closed</span>';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function open(Request $request)
    {
        $request->validate([
            'start_cash' => 'required|numeric|min:0',
        ]);

        try {
            $shift = $this->service->openShift([
                'start_cash' => $request->start_cash,
                'branch_id' => Auth::user()->branch_id ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift kasir berhasil dibuka.',
                'shift' => $shift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function close(Request $request, $uuid)
    {
        $request->validate([
            'actual_end_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $shift = $this->service->closeShift($uuid, [
                'actual_end_cash' => $request->actual_end_cash,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift kasir berhasil ditutup.',
                'shift' => $shift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
