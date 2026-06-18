<?php

namespace App\Models\Logistic\Master\Branch;

use App\Models\Logistic\Master\BaseMasterModel;

class Branch extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'code',
        'name',
        'city',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];
}
