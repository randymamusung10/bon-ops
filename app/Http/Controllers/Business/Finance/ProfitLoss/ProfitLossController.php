<?php

namespace App\Http\Controllers\Business\Finance\ProfitLoss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Fetch non-header accounts for revenue and expense
        $accounts = ChartOfAccount::with(['parent', 'generalLedgers' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->where('tenant_id', $tenantId)
            ->whereIn('account_type', ['revenue', 'expense'])
            ->where('is_header', false)
            ->get();

        $revenues = [];
        $cogs = [];
        $expenses = [];

        $totalRevenue = 0;
        $totalCogs = 0;
        $totalExpense = 0;

        foreach ($accounts as $account) {
            $debit = $account->generalLedgers->sum('debit');
            $credit = $account->generalLedgers->sum('credit');

            $groupName = $account->parent ? $account->parent->name : 'Lain-lain';

            if ($account->account_type === 'revenue') {
                $balance = $credit - $debit;
                if ($balance != 0) {
                    $revenues[$groupName][] = [
                        'code' => $account->code,
                        'name' => $account->name,
                        'balance' => $balance
                    ];
                    $totalRevenue += $balance;
                }
            } elseif ($account->account_type === 'expense') {
                $balance = $debit - $credit;
                if ($balance != 0) {
                    if (str_starts_with($account->code, '51')) {
                        $cogs[$groupName][] = [
                            'code' => $account->code,
                            'name' => $account->name,
                            'balance' => $balance
                        ];
                        $totalCogs += $balance;
                    } else {
                        $expenses[$groupName][] = [
                            'code' => $account->code,
                            'name' => $account->name,
                            'balance' => $balance
                        ];
                        $totalExpense += $balance;
                    }
                }
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpense;

        return view('pages.business.finance.profit_loss.index', compact(
            'startDate', 'endDate', 
            'revenues', 'cogs', 'expenses', 
            'totalRevenue', 'totalCogs', 'totalExpense', 
            'grossProfit', 'netProfit'
        ));
    }
}
