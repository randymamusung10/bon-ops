<?php

namespace App\Models\MasterData;

class ProductCategory extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'parent_id', 'uuid', 'code', 'name',
        'description', 'status', 'created_by', 'updated_by'
    ];
    
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
