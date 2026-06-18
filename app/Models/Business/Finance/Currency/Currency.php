<?php

namespace App\Models\Business\Finance\Currency;

use App\Models\Logistic\Master\BaseMasterModel;

class Currency extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'symbol', 'exchange_rate', 'status', 'created_by', 'updated_by'
    ];
    
    protected $casts = [
        'exchange_rate' => 'decimal:6',
    ];
}
