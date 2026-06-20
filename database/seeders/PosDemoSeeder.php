<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Operational\Pos\PosShift;
use App\Models\Operational\Pos\PosOrder;
use App\Models\Operational\Pos\PosOrderItem;
use App\Services\Operational\Pos\PosOrderService;
use Illuminate\Support\Facades\Auth;

class PosDemoSeeder extends Seeder
{
    public function run()
    {
        Auth::loginUsingId(1);

        $tenantId = 1;
        $branch = Branch::where('tenant_id', $tenantId)->first();
        $warehouse = Warehouse::where('tenant_id', $tenantId)->first();
        
        if (!$branch || !$warehouse) {
            $this->command->error('Seeder Gagal: Cabang atau Gudang default tidak ditemukan.');
            return;
        }

        $products = Product::where('tenant_id', $tenantId)->limit(4)->get();
        if ($products->isEmpty()) {
            $this->command->error('Seeder Gagal: Produk tidak ditemukan.');
            return;
        }

        // 1. Create a closed shift from yesterday
        $closedShift = PosShift::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'user_id' => 1,
            'start_time' => now()->subDay()->setTime(8, 0, 0),
            'end_time' => now()->subDay()->setTime(17, 0, 0),
            'start_cash' => 100000,
            'end_cash' => 350000,
            'actual_end_cash' => 350000,
            'notes' => 'Shift lancar, kas cocok.',
            'status' => 'closed',
        ]);

        // Create an order in yesterday's shift
        $yesterdayOrder = PosOrder::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'pos_shift_id' => $closedShift->id,
            'order_number' => 'POS-YEST-' . time(),
            'date' => now()->subDay()->toDateString(),
            'total_amount' => 250000,
            'tax_amount' => 25000,
            'grand_total' => 275000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'customer_name' => 'Budi',
            'table_number' => 'Meja 5',
            'created_by' => 1,
        ]);

        foreach ($products as $product) {
            PosOrderItem::create([
                'pos_order_id' => $yesterdayOrder->id,
                'product_id' => $product->id,
                'qty' => 2,
                'price' => $product->price ?? 25000,
                'tax_amount' => ($product->price ?? 25000) * 0.10,
                'subtotal' => ($product->price ?? 25000) * 2,
                'status' => 'completed',
            ]);
        }

        // 2. Create an open shift for today
        $openShift = PosShift::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'user_id' => 1,
            'start_time' => now()->setTime(8, 0, 0),
            'start_cash' => 100000,
            'status' => 'open',
        ]);

        // Create a pending/processing order in today's shift (for KDS/BDS testing)
        $todayOrder = PosOrder::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branch->id,
            'pos_shift_id' => $openShift->id,
            'order_number' => 'POS-TODAY-' . time(),
            'date' => now()->toDateString(),
            'total_amount' => 100000,
            'tax_amount' => 10000,
            'grand_total' => 110000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'processing',
            'customer_name' => 'Siti',
            'table_number' => 'Meja 3',
            'created_by' => 1,
        ]);

        // Order item 1: Biji Kopi (Usually barista station)
        $prodCoffee = Product::where('tenant_id', $tenantId)->where('name', 'like', '%kopi%')->first() ?? $products->first();
        PosOrderItem::create([
            'pos_order_id' => $todayOrder->id,
            'product_id' => $prodCoffee->id,
            'qty' => 1,
            'price' => $prodCoffee->price ?? 35000,
            'tax_amount' => ($prodCoffee->price ?? 35000) * 0.10,
            'subtotal' => $prodCoffee->price ?? 35000,
            'status' => 'pending',
            'notes' => 'Less ice, extra sweet'
        ]);

        // Order item 2: Food (Usually kitchen station)
        $prodFood = Product::where('tenant_id', $tenantId)->where('name', 'not like', '%kopi%')->first() ?? $products->last();
        PosOrderItem::create([
            'pos_order_id' => $todayOrder->id,
            'product_id' => $prodFood->id,
            'qty' => 1,
            'price' => $prodFood->price ?? 45000,
            'tax_amount' => ($prodFood->price ?? 45000) * 0.10,
            'subtotal' => $prodFood->price ?? 45000,
            'status' => 'pending',
            'notes' => 'Pedas sedang'
        ]);
        
        // Run stock deduction for today's order
        $orderService = app(PosOrderService::class);
        $orderService->deductInventoryForOrder($todayOrder);
    }
}
