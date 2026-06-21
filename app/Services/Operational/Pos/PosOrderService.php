<?php

namespace App\Services\Operational\Pos;

use App\Models\Operational\Pos\PosOrder;
use App\Models\Operational\Pos\PosOrderItem;
use App\Models\Logistic\Master\Recipe\Recipe;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Inventory\InventoryBalance;
use App\Models\Logistic\Inventory\InventoryMovement;
use App\Repositories\Operational\Pos\PosOrderRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PosOrderService
{
    protected $repository;

    public function __construct(PosOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createOrder(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $branchId = Auth::user()->branch_id ?? $data['branch_id'];
            $userId = Auth::id();

            // Find active shift
            $activeShift = DB::table('pos_shifts')
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                throw new Exception("Transaksi gagal. Anda harus membuka shift kasir terlebih dahulu.");
            }

            // Generate order number
            $orderNo = 'POS-' . date('YmdHis') . '-' . rand(100, 999);

            $order = $this->repository->create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'pos_shift_id' => $activeShift->id,
                'order_number' => $orderNo,
                'date' => now()->toDateString(),
                'total_amount' => $data['total_amount'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'grand_total' => $data['grand_total'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_status' => $data['payment_status'] ?? 'paid',
                'status' => $data['status'] ?? 'completed',
                'order_type' => $data['order_type'] ?? 'dine-in',
                'customer_name' => $data['customer_name'] ?? null,
                'table_number' => $data['table_number'] ?? null,
                'created_by' => $userId,
                'notes' => $data['notes'] ?? null,
                'due_date' => $data['due_date'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $subtotal = $item['price'] * $item['qty'];
                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $subtotal,
                    'status' => 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // If order is paid, deduct inventory
            if ($order->payment_status === 'paid') {
                $this->deductInventoryForOrder($order);
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrder(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $userId = Auth::id();

            $order = \App\Models\Operational\Pos\PosOrder::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();

            if ($order->payment_status === 'paid') {
                throw new Exception("Pesanan sudah dilunasi dan tidak dapat diubah.");
            }

            // Update order details
            $order->update([
                'total_amount' => $data['total_amount'],
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'grand_total' => $data['grand_total'],
                'payment_method' => $data['payment_method'] ?? $order->payment_method,
                'payment_status' => $data['payment_status'] ?? $order->payment_status,
                'status' => $data['status'] ?? $order->status,
                'order_type' => $data['order_type'] ?? $order->order_type,
                'customer_name' => $data['customer_name'] ?? $order->customer_name,
                'table_number' => $data['table_number'] ?? $order->table_number,
                'updated_by' => $userId,
                'notes' => $data['notes'] ?? $order->notes,
                'due_date' => $data['due_date'] ?? $order->due_date,
            ]);

            // Remove old items
            PosOrderItem::where('pos_order_id', $order->id)->delete();

            // Re-insert new items
            foreach ($data['items'] as $item) {
                $subtotal = $item['price'] * $item['qty'];
                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $subtotal,
                    'status' => 'pending',
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Reload items
            $order->load('items');

            // If order is paid, deduct inventory
            if ($order->payment_status === 'paid') {
                $this->deductInventoryForOrder($order);
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deductInventoryForOrder(PosOrder $order)
    {
        $tenantId = $order->tenant_id;
        $branchId = $order->branch_id;

        // Find primary warehouse for this branch
        $warehouse = Warehouse::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->first();

        if (!$warehouse) {
            // If no active warehouse for this branch, fall back to first warehouse in the branch
            $warehouse = Warehouse::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->first();
        }

        if (!$warehouse) {
            // If still no warehouse, cannot deduct stock
            return;
        }

        foreach ($order->items as $item) {
            // Check if product has active recipe
            $recipe = Recipe::where('tenant_id', $tenantId)
                ->where('product_id', $item->product_id)
                ->where('status', 'active')
                ->first();

            if ($recipe) {
                // Deduct based on recipe items
                foreach ($recipe->items as $recipeItem) {
                    $usageQty = ($recipeItem->quantity / ($recipe->quantity ?: 1)) * $item->qty;
                    $this->deductStock(
                        $tenantId, 
                        $branchId, 
                        $warehouse->id, 
                        $recipeItem->product_id, 
                        $usageQty, 
                        $order, 
                        "Bahan resep untuk menu: " . $item->product->name
                    );
                }
            } else {
                // Deduct the product itself directly
                $this->deductStock(
                    $tenantId, 
                    $branchId, 
                    $warehouse->id, 
                    $item->product_id, 
                    $item->qty, 
                    $order, 
                    "Penjualan langsung POS"
                );
            }
        }
    }

    private function deductStock(int $tenantId, int $branchId, int $warehouseId, int $productId, float $qty, PosOrder $order, string $notes)
    {
        $balance = InventoryBalance::where([
            'tenant_id' => $tenantId,
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
        ])->lockForUpdate()->first();

        if (!$balance) {
            $balance = InventoryBalance::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'qty' => 0
            ]);
        }

        $newBalance = $balance->qty - $qty;

        // Create Movement
        InventoryMovement::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'reference_type' => 'pos_order',
            'reference_id' => $order->id,
            'reference_number' => $order->order_number,
            'date' => $order->date,
            'qty_in' => 0,
            'qty_out' => $qty,
            'balance_after' => $newBalance,
            'notes' => $notes,
        ]);

        // Update balance
        $balance->update(['qty' => $newBalance]);
    }

    public function updateOrderItemStatus(int $itemId, string $status)
    {
        try {
            DB::beginTransaction();

            $orderItem = PosOrderItem::findOrFail($itemId);
            $orderItem->update(['status' => $status]);

            $order = $orderItem->posOrder;

            // If the order was pending and an item starts cooking/completed, change order to processing
            if ($order->status === 'pending' && in_array($status, ['cooking', 'completed'])) {
                $order->update(['status' => 'processing']);
            }

            // If all items are completed, update order status to completed
            $allCompleted = !PosOrderItem::where('pos_order_id', $order->id)
                ->where('status', '!=', 'completed')
                ->exists();

            if ($allCompleted && $order->status === 'processing') {
                $order->update(['status' => 'completed']);
            }

            DB::commit();
            return $orderItem;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function refundOrder(string $uuid, ?string $notes = null)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $order = PosOrder::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();

            if ($order->payment_status === 'refunded') {
                throw new Exception("Transaksi ini sudah di-refund sebelumnya.");
            }

            // Check if any order item has been started (status is cooking or completed)
            $hasStartedItems = $order->items()->whereIn('status', ['cooking', 'completed'])->exists();
            if ($hasStartedItems) {
                throw new Exception("Tidak dapat melakukan refund. Pesanan sudah mulai diproses di dapur/barista.");
            }

            // Update order status
            $order->update([
                'payment_status' => 'refunded',
                'status' => 'cancelled',
                'notes' => ($order->notes ? $order->notes . "\n" : "") . "Refund Notes: " . ($notes ?? 'Customer cancel order'),
            ]);

            // Update all order items status to cancelled
            $order->items()->update(['status' => 'cancelled']);

            // Revert inventory deductions if they were deducted
            $movements = InventoryMovement::where('tenant_id', $tenantId)
                ->where('reference_type', 'pos_order')
                ->where('reference_id', $order->id)
                ->where('qty_out', '>', 0)
                ->get();

            foreach ($movements as $movement) {
                // Find balance
                $balance = InventoryBalance::where([
                    'tenant_id' => $tenantId,
                    'warehouse_id' => $movement->warehouse_id,
                    'product_id' => $movement->product_id,
                ])->lockForUpdate()->first();

                if ($balance) {
                    $newQty = $balance->qty + $movement->qty_out;
                    $balance->update(['qty' => $newQty]);

                    // Log positive movement to record the return
                    InventoryMovement::create([
                        'tenant_id' => $tenantId,
                        'branch_id' => $order->branch_id,
                        'warehouse_id' => $movement->warehouse_id,
                        'product_id' => $movement->product_id,
                        'reference_type' => 'pos_order',
                        'reference_id' => $order->id,
                        'reference_number' => $order->order_number,
                        'date' => now()->toDateString(),
                        'qty_in' => $movement->qty_out,
                        'qty_out' => 0,
                        'balance_after' => $newQty,
                        'notes' => "Pengembalian stok akibat pembatalan/refund pesanan " . $order->order_number,
                    ]);
                }
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
