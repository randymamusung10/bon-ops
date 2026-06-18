<?php

namespace App\Models\Logistic\Master\Warehouse;

use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\BaseMasterModel;

class Warehouse extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'branch_id', 'uuid', 'code', 'name',
        'address', 'city', 'status', 'created_by', 'updated_by'
    ];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
