<?php

namespace App\Models\Business\Finance\ChartOfAccount;

use App\Models\Logistic\Master\BaseMasterModel;

class ChartOfAccount extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name', 
        'account_type', 'is_header', 'parent_id', 'status', 
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_header' => 'boolean'
    ];

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function generalLedgers()
    {
        return $this->hasMany(\App\Models\Business\Finance\GeneralLedger\GeneralLedger::class, 'chart_of_account_id');
    }
}
