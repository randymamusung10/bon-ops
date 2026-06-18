<?php

namespace App\Models\MasterData;

class ChartOfAccount extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'account_type', 'status', 'created_by', 'updated_by'
    ];
}
