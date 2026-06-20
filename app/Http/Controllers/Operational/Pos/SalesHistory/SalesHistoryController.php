<?php

namespace App\Http\Controllers\Operational\Pos\SalesHistory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Operational\Pos\PosOrder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SalesHistoryController extends Controller
{
    public function index()
    {
        return view('pages.operational.pos.history.index');
    }

    public function data()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $orders = PosOrder::with(['creator', 'branch'])
            ->where('tenant_id', $tenantId)
            ->latest();

        return DataTables::of($orders)
            ->editColumn('date', function ($model) {
                return $model->created_at ? $model->created_at->format('d/m/Y H:i') : '-';
            })
            ->editColumn('grand_total', function ($model) {
                return 'Rp ' . number_format($model->grand_total, 0, ',', '.');
            })
            ->addColumn('payment_status_badge', function ($model) {
                if ($model->payment_status === 'paid') {
                    return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i> Paid</span>';
                } elseif ($model->payment_status === 'refunded') {
                    return '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-arrow-counterclockwise me-1"></i> Refunded</span>';
                }
                return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-exclamation-circle-fill me-1"></i> Unpaid</span>';
            })
            ->addColumn('status_badge', function ($model) {
                if ($model->status === 'completed') {
                    return '<span class="badge bg-success px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-check-lg me-1"></i> Completed</span>';
                } elseif ($model->status === 'cancelled') {
                    return '<span class="badge bg-danger px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-x-circle me-1"></i> Cancelled</span>';
                } elseif ($model->status === 'processing') {
                    return '<span class="badge bg-primary px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;"><i class="bi bi-clock me-1"></i> Kitchen</span>';
                }
                return '<span class="badge bg-secondary px-2 py-1 rounded-pill" style="font-size: 11px; font-weight:600;">' . ucfirst($model->status) . '</span>';
            })
            ->rawColumns(['payment_status_badge', 'status_badge'])
            ->make(true);
    }

    public function detail($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = PosOrder::with(['items.product', 'creator', 'branch'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}
