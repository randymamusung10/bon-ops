<?php

namespace App\Http\Controllers\Business\Finance\BalanceSheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $asOfDate = $request->input('as_of_date', Carbon::now()->format('Y-m-d'));

        // Fetch all non-header accounts and their ledgers up to the As-Of Date
        $accounts = ChartOfAccount::with(['parent', 'generalLedgers' => function ($query) use ($asOfDate) {
                $query->where('date', '<=', $asOfDate);
            }])
            ->where('tenant_id', $tenantId)
            ->where('is_header', false)
            ->get();

        $assets = [];
        $liabilities = [];
        $equities = [];
        
        $totalAsset = 0;
        $totalLiability = 0;
        $totalEquity = 0;
        $currentEarnings = 0; // Accumulated Net Profit

        foreach ($accounts as $account) {
            $debit = $account->generalLedgers->sum('debit');
            $credit = $account->generalLedgers->sum('credit');
            $groupName = $account->parent ? $account->parent->name : 'Lain-lain';

            if ($account->account_type === 'asset') {
                $balance = $debit - $credit; // Asset normal balance is debit
                if ($balance != 0) {
                    $assets[$groupName][] = [
                        'code' => $account->code, 'name' => $account->name, 'balance' => $balance
                    ];
                    $totalAsset += $balance;
                }
            } elseif ($account->account_type === 'liability') {
                $balance = $credit - $debit; // Liability normal balance is credit
                if ($balance != 0) {
                    $liabilities[$groupName][] = [
                        'code' => $account->code, 'name' => $account->name, 'balance' => $balance
                    ];
                    $totalLiability += $balance;
                }
            } elseif ($account->account_type === 'equity') {
                $balance = $credit - $debit; // Equity normal balance is credit
                if ($balance != 0) {
                    $equities[$groupName][] = [
                        'code' => $account->code, 'name' => $account->name, 'balance' => $balance
                    ];
                    $totalEquity += $balance;
                }
            } elseif ($account->account_type === 'revenue') {
                $balance = $credit - $debit;
                $currentEarnings += $balance;
            } elseif ($account->account_type === 'expense') {
                $balance = $debit - $credit;
                $currentEarnings -= $balance;
            }
        }

        $totalEquityAndLiabilities = $totalLiability + $totalEquity + $currentEarnings;

        return view('pages.business.finance.balance_sheet.index', compact(
            'asOfDate', 'assets', 'liabilities', 'equities',
            'totalAsset', 'totalLiability', 'totalEquity', 'currentEarnings',
            'totalEquityAndLiabilities'
        ));
    }
}
