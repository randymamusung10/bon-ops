<?php

namespace App\Http\Controllers\Operational\Pos\PosTerminal;

use App\Http\Controllers\Controller;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use App\Repositories\Operational\Pos\PosShiftRepository;
use App\Services\Operational\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosTerminalController extends Controller
{
    protected $shiftRepository;
    protected $orderService;

    public function __construct(PosShiftRepository $shiftRepository, PosOrderService $orderService)
    {
        $this->shiftRepository = $shiftRepository;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $userId = Auth::id();

        // Ensure user has an active shift before letting them enter POS
        $activeShift = $this->shiftRepository->getActiveShift($tenantId, $userId);
        if (!$activeShift) {
            return redirect()->route('operational.pos.shift')->with('error', 'Anda harus membuka shift kasir sebelum mengakses POS Terminal.');
        }

        // Fetch products and categories for POS screen
        $categories = ProductCategory::where('tenant_id', $tenantId)->where('status', 'active')->get();
        
        $branchId = $activeShift->branch_id;

        // POS usually sells finished goods or menu items.
        $products = Product::with(['unit', 'category', 'inventoryBalances' => function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        }])
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        return view('pages.operational.pos.terminal.index', compact('activeShift', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'table_number' => 'nullable|string|max:20',
            'order_type' => 'required|string|in:dine-in,take-away,online',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            // Calculate total, tax, and grand total
            $totalAmount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['price'];
                $totalAmount += $subtotal;
                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                ];
            }

            // Assume 10% VAT tax
            $taxAmount = $totalAmount * 0.10;
            $grandTotal = $totalAmount + $taxAmount;

            $orderData = [
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'tempo' ? 'unpaid' : 'paid',
                'status' => 'pending', // starts as pending, will become processing when kitchen/barista starts cooking
                'order_type' => $request->order_type,
                'customer_name' => $request->customer_name,
                'table_number' => $request->table_number,
                'notes' => $request->notes,
                'due_date' => $request->due_date,
                'items' => $itemsData,
            ];

            if ($request->has('order_uuid') && !empty($request->order_uuid)) {
                $order = $this->orderService->updateOrder($request->order_uuid, $orderData);
            } else {
                $order = $this->orderService->createOrder($orderData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses.',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function unpaidOrders(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $orders = \App\Models\Operational\Pos\PosOrder::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function orderDetail($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = \App\Models\Operational\Pos\PosOrder::with('items.product')
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function receipt($uuid)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $order = \App\Models\Operational\Pos\PosOrder::with(['items.product', 'creator'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('pages.operational.pos.terminal.receipt', compact('order'));
    }
}
