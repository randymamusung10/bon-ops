<?php

namespace App\Models\Logistic\Master\Product;

use App\Models\Logistic\Master\BaseMasterModel;
use App\Models\Logistic\Master\ProductCategory\ProductCategory;
use App\Models\Logistic\Master\Unit\Unit;
use App\Models\Business\Finance\Tax\Tax;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;

class Product extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'product_category_id', 'unit_id',
        'inventory_account_id', 'cogs_account_id', 'income_account_id', 'tax_id',
        'uuid', 'code', 'name', 'type', 'price', 'cost', 'description', 
        'status', 'created_by', 'updated_by'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    
    public function inventoryAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'inventory_account_id');
    }
    
    public function cogsAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cogs_account_id');
    }
    
    public function incomeAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'income_account_id');
    }
}
