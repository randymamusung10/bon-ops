<?php

namespace App\Http\Controllers\Operational\Restaurant\KitchenDisplay;

use App\Http\Controllers\Controller;
use App\Models\Operational\Pos\PosOrderItem;
use App\Services\Operational\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenDisplayController extends Controller
{
    protected $orderService;

    public function __construct(PosOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        // Fetch active order items for kitchen
        $items = PosOrderItem::with(['posOrder', 'product'])
            ->whereHas('posOrder', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->whereIn('status', ['pending', 'processing']);
            })
            ->where('status', '!=', 'completed')
            ->where(function($q) {
                $q->whereHas('product.recipe', function($qr) {
                    $qr->whereHas('station', function($qs) {
                        $qs->where('name', 'not like', '%barista%');
                    });
                })->orWhereDoesntHave('product.recipe');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.operational.restaurant.kitchen.index', compact('items'));
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
                'message' => 'Status pesanan dapur berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
