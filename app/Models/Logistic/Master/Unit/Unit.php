<?php

namespace App\Models\Logistic\Master\Unit;

use App\Models\Logistic\Master\BaseMasterModel;

class Unit extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'description', 'status', 'created_by', 'updated_by'
    ];
}
