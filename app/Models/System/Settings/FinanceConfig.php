<?php

namespace App\Models\System\Settings;

use Illuminate\Database\Eloquent\Model;

class FinanceConfig extends Model
{
    protected $table = 'finance_configs';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'cash_account_id',
        'sales_revenue_account_id',
        'tax_payable_account_id',
        'cogs_account_id',
        'inventory_account_id',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Logistic\Master\Branch\Branch::class, 'branch_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'cash_account_id');
    }

    public function salesRevenueAccount()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'sales_revenue_account_id');
    }

    public function taxPayableAccount()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'tax_payable_account_id');
    }

    public function cogsAccount()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'cogs_account_id');
    }

    public function inventoryAccount()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'inventory_account_id');
    }
}
