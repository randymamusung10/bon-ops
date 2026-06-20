<?php

namespace App\Models\Logistic\Master\Recipe;

use App\Models\Logistic\Master\BaseMasterModel;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\ProductionStation\ProductionStation;

class Recipe extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id',
        'company_id',
        'product_id',
        'production_station_id',
        'uuid',
        'code',
        'name',
        'quantity',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'status' => 'string'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function station()
    {
        return $this->belongsTo(ProductionStation::class, 'production_station_id');
    }

    public function items()
    {
        return $this->hasMany(RecipeItem::class, 'recipe_id');
    }
}
