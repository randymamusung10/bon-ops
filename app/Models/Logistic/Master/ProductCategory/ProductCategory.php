<?php

namespace App\Models\Logistic\Master\ProductCategory;

use App\Models\Logistic\Master\BaseMasterModel;

class ProductCategory extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'parent_id', 'uuid', 'code', 'name',
        'description', 'status', 'created_by', 'updated_by'
    ];
    
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }
    
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}
