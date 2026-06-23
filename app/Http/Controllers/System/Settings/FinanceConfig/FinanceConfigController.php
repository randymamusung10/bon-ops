<?php

namespace App\Http\Controllers\System\Settings\FinanceConfig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Settings\FinanceConfig;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;

class FinanceConfigController extends Controller
{
    public function index()
    {
        // For multi-tenant or multi-branch, we could filter this.
        // Assuming default first config for now.
        $config = FinanceConfig::first();
        
        $coas = ChartOfAccount::where('status', 'active')->orderBy('code')->get();

        return view('pages.system.settings.finance_config.index', compact('config', 'coas'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cash_account_id' => 'nullable|exists:chart_of_accounts,id',
            'sales_revenue_account_id' => 'nullable|exists:chart_of_accounts,id',
            'tax_payable_account_id' => 'nullable|exists:chart_of_accounts,id',
            'cogs_account_id' => 'nullable|exists:chart_of_accounts,id',
            'inventory_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $config = FinanceConfig::first();

        if (!$config) {
            $config = new FinanceConfig();
            // Assign default tenant_id / branch_id if necessary in the future
            $config->tenant_id = 1;
            $config->branch_id = 1;
        }

        $config->fill($request->only([
            'cash_account_id',
            'sales_revenue_account_id',
            'tax_payable_account_id',
            'cogs_account_id',
            'inventory_account_id'
        ]));
        
        $config->save();

        return redirect()->back()->with('success', 'Konfigurasi keuangan berhasil disimpan.');
    }
}
