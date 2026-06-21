<?php

namespace App\Http\Controllers\Business\Finance\AccountsReceivable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operational\Pos\PosOrder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class AccountsReceivableController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.receivable.index');
    }

    public function data(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        // Fetch POS orders that are not fully paid (acts as AR)
        // Mengambil transaksi yang memang merupakan piutang (Tempo) atau belum lunas
        $query = PosOrder::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('payment_method', 'tempo')
                  ->orWhereIn('payment_status', ['unpaid', 'partial']);
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('order_number', function ($row) {
                return $row->order_number;
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d/m/Y');
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer_name ?: 'Pelanggan Umum';
            })
            ->editColumn('grand_total', function ($row) {
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('remaining_balance', function ($row) {
                if ($row->payment_status === 'paid') {
                    return number_format(0, 2, ',', '.');
                }
                return number_format($row->grand_total, 2, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->payment_status === 'paid') {
                    return '<span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Lunas</span>';
                }
                if ($row->payment_status === 'partial') {
                    return '<span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Dibayar Sebagian</span>';
                }
                return '<span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Belum Dibayar</span>';
            })
            ->addColumn('action', function ($row) {
                if ($row->payment_status === 'paid') {
                    return '<span class="text-muted"><i class="bi bi-check-lg"></i></span>';
                }
                return '<div class="d-inline-flex gap-2"><button class="btn-icon-modern text-success btn-pay" data-uuid="'.$row->uuid.'" title="Lunasi Piutang" style="background: rgba(16, 185, 129, 0.12);"><i class="bi bi-check2-circle"></i></button></div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function pay($uuid)
    {
        try {
            $tenantId = Auth::user()->tenant_id ?? 1;
            $order = PosOrder::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
            
            $order->payment_status = 'paid';
            // We do not change payment_method to 'cash', so it remains 'tempo' to signify it was originally a receivable.
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Piutang berhasil dilunasi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
