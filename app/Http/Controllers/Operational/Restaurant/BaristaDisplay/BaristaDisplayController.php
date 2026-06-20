<?php

namespace App\Http\Controllers\Operational\Restaurant\BaristaDisplay;

use App\Http\Controllers\Controller;
use App\Models\Operational\Pos\PosOrderItem;
use App\Services\Operational\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaristaDisplayController extends Controller
{
    protected $orderService;

    public function __construct(PosOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        // Fetch active order items for barista
        $items = PosOrderItem::with(['posOrder', 'product'])
            ->whereHas('posOrder', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->whereIn('status', ['pending', 'processing']);
            })
            ->where('status', '!=', 'completed')
            ->whereHas('product.recipe', function($qr) {
                $qr->whereHas('station', function($qs) {
                    $qs->where('name', 'like', '%barista%');
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.operational.restaurant.barista.index', compact('items'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:cooking,completed',
        ]);

        try {
            $this->orderService->updateOrderItemStatus($id, $request->status);
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan barista berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
