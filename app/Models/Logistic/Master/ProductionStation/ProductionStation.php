<?php

namespace App\Models\Logistic\Master\ProductionStation;

use App\Models\Logistic\Master\BaseMasterModel;

class ProductionStation extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'code',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'string'
    ];
}
