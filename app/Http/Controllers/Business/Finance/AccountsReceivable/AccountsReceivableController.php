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
            ->whereNotNull('due_date')
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
                $remaining = max(0, $row->grand_total - $row->paid_amount);
                return number_format($remaining, 2, ',', '.');
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
                $btnShow = '<button class="btn-icon-modern text-info btn-show" data-uuid="'.$row->uuid.'" title="Detail" style="background: rgba(14, 165, 233, 0.12);"><i class="bi bi-eye"></i></button>';
                if ($row->payment_status === 'paid') {
                    return '<div class="d-inline-flex gap-2">'.$btnShow.'</div>';
                }
                $btnPay = '<button class="btn-icon-modern text-success btn-pay" data-uuid="'.$row->uuid.'" title="Lunasi Piutang" style="background: rgba(16, 185, 129, 0.12);"><i class="bi bi-check2-circle"></i></button>';
                return '<div class="d-inline-flex gap-2">'.$btnShow.$btnPay.'</div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function paymentModal($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = PosOrder::with('payments.creator')->where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        return view('pages.business.finance.receivable.partials.payment_modal', compact('order', 'uuid'));
    }

    public function showModal($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = PosOrder::with(['items.product', 'payments.creator'])->where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        return view('pages.business.finance.receivable.partials.show_modal', compact('order'));
    }

    public function pay(Request $request, $uuid)
    {
        try {
            $request->validate([
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:1',
                'payment_method' => 'required|string',
                'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120'
            ]);

            $tenantId = Auth::user()->tenant_id ?? 1;
            $order = PosOrder::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payments', $filename, 'public');
                $attachmentPath = 'storage/payments/' . $filename;
            }

            $order->payments()->create([
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'attachment_path' => $attachmentPath,
                'notes' => $request->payment_notes,
                'created_by' => Auth::id()
            ]);

            $newPaidAmount = $order->paid_amount; // Note: Since paid_amount is dynamically calculated, it will reflect the newly inserted payment immediately.
            
            if ($newPaidAmount >= $order->grand_total) {
                $order->payment_status = 'paid';
                $order->paid_at = $request->payment_date;
            } else {
                $order->payment_status = 'partial';
            }
            
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
