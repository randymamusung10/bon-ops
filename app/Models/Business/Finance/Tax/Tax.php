<?php

namespace App\Models\Business\Finance\Tax;

use App\Models\Logistic\Master\BaseMasterModel;

class Tax extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'rate_percentage', 'status', 'created_by', 'updated_by'
    ];
    
    protected $casts = [
        'rate_percentage' => 'decimal:4',
    ];
}
