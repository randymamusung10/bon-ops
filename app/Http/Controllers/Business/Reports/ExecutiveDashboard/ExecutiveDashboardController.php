<?php

namespace App\Http\Controllers\Business\Reports\ExecutiveDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operational\Pos\PosOrder;
use App\Models\Operational\Pos\PosOrderItem;
use App\Models\Logistic\Purchasing\SupplierInvoice;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $now = Carbon::now();
        
        $startDateParam = $request->input('start_date');
        $endDateParam = $request->input('end_date');

        if ($startDateParam && $endDateParam) {
            $startDate = Carbon::parse($startDateParam)->startOfDay();
            $endDate = Carbon::parse($endDateParam)->endOfDay();
        } else {
            $startDate = $now->copy()->startOfMonth();
            $endDate = $now->copy()->endOfMonth();
        }

        $startOfLastPeriod = $startDate->copy()->subDays($startDate->diffInDays($endDate) + 1)->startOfDay();
        $endOfLastPeriod = $startDate->copy()->subDay()->endOfDay();

        // 1. Total Sales (Revenue)
        $salesThisPeriod = PosOrder::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('grand_total');
            
        $salesLastPeriod = PosOrder::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereBetween('date', [$startOfLastPeriod, $endOfLastPeriod])
            ->sum('grand_total');

        $salesGrowth = $this->calculateGrowth($salesThisPeriod, $salesLastPeriod);

        // 1b. COGS & Net Profit
        $cogsThisPeriod = PosOrderItem::join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->where('pos_orders.tenant_id', $tenantId)
            ->whereIn('pos_orders.payment_status', ['paid', 'partial'])
            ->whereBetween('pos_orders.date', [$startDate, $endDate])
            ->sum(DB::raw('pos_order_items.qty * products.cost'));
            
        $netProfitThisPeriod = $salesThisPeriod - $cogsThisPeriod;

        // 2. Total Expenses (Purchasing)
        $expensesThisPeriod = SupplierInvoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['posted', 'paid'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('grand_total');

        $expensesLastPeriod = SupplierInvoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['posted', 'paid'])
            ->whereBetween('date', [$startOfLastPeriod, $endOfLastPeriod])
            ->sum('grand_total');

        $expensesGrowth = $this->calculateGrowth($expensesThisPeriod, $expensesLastPeriod);

        // 3. POS Transactions Count
        $transactionsCount = PosOrder::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();
            
        $itemsSoldThisPeriod = PosOrderItem::join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->where('pos_orders.tenant_id', $tenantId)
            ->whereIn('pos_orders.payment_status', ['paid', 'partial'])
            ->whereBetween('pos_orders.date', [$startDate, $endDate])
            ->sum('pos_order_items.qty');
            
        // Additional Metrics
        $totalReceivable = PosOrder::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum(DB::raw('grand_total - IFNULL((SELECT SUM(amount) FROM pos_order_payments WHERE pos_order_id = pos_orders.id), 0)'));

        $avgTransactionValue = $transactionsCount > 0 ? $salesThisPeriod / $transactionsCount : 0;
        
        $stockValuation = \App\Models\Logistic\Inventory\InventoryBalance::where('inventory_balances.tenant_id', $tenantId)
            ->join('products', 'inventory_balances.product_id', '=', 'products.id')
            ->sum(DB::raw('inventory_balances.qty * products.cost'));

        // 4. Sales Trend
        $salesTrendData = PosOrder::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw('DATE(date) as trend_date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('trend_date')
            ->orderBy('trend_date')
            ->get();

        $trendDates = [];
        $trendTotals = [];
        $currentDate = $startDate->copy();
        
        // Limit points to prevent chart overflow if date range is huge. Max 60 points.
        $stepDays = 1;
        $totalDays = $startDate->diffInDays($endDate);
        if ($totalDays > 60) {
            $stepDays = ceil($totalDays / 60);
        }

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $trendDates[] = $currentDate->format('d M');
            
            // If stepping, we sum the totals for the step range
            $sumForStep = 0;
            for ($i = 0; $i < $stepDays; $i++) {
                $stepDateStr = $currentDate->copy()->addDays($i)->format('Y-m-d');
                $found = $salesTrendData->firstWhere('trend_date', $stepDateStr);
                if ($found) {
                    $sumForStep += (float)$found->total;
                }
            }
            $trendTotals[] = $sumForStep;
            
            $currentDate->addDays($stepDays);
        }

        // 5. Top Selling Products
        $topProducts = PosOrderItem::join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->where('pos_orders.tenant_id', $tenantId)
            ->whereBetween('pos_orders.date', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(pos_order_items.qty) as total_qty'), DB::raw('SUM(pos_order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // 6. Recent Transactions
        $recentTransactions = PosOrder::where('tenant_id', $tenantId)
            ->with('creator')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.business.reports.executive.index', compact(
            'startDate', 'endDate',
            'salesThisPeriod', 'salesGrowth',
            'netProfitThisPeriod',
            'expensesThisPeriod', 'expensesGrowth',
            'transactionsCount', 'itemsSoldThisPeriod',
            'totalReceivable', 'avgTransactionValue', 'stockValuation',
            'trendDates', 'trendTotals',
            'topProducts', 'recentTransactions'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}
