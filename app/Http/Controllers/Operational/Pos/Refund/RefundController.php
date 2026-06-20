<?php

namespace App\Http\Controllers\Operational\Pos\Refund;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Operational\Pos\PosOrder;
use App\Services\Operational\Pos\PosOrderService;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    protected $orderService;

    public function __construct(PosOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return view('pages.operational.pos.refund.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:100',
        ]);

        $tenantId = Auth::user()->tenant_id ?? 1;
        // Strip all leading/trailing whitespace including tabs, non-breaking spaces, and control characters
        $searchQuery = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $request->order_number);

        // 1. Try exact match (case-insensitive)
        $order = PosOrder::with(['items.product', 'creator', 'branch'])
            ->where('tenant_id', $tenantId)
            ->whereRaw('LOWER(order_number) = ?', [strtolower($searchQuery)])
            ->first();

        // 2. Fallback to partial match if not found
        if (!$order) {
            $order = PosOrder::with(['items.product', 'creator', 'branch'])
                ->where('tenant_id', $tenantId)
                ->where('order_number', 'LIKE', '%' . $searchQuery . '%')
                ->first();
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan. Pastikan Nomor Invoice yang dimasukkan benar.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function process(Request $request, $uuid)
    {
        $request->validate([
            'notes' => 'required|string|max:255',
        ]);

        try {
            $order = $this->orderService->refundOrder($uuid, $request->notes);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi ' . $order->order_number . ' berhasil dibatalkan (Refund) dan stok dikembalikan.',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
