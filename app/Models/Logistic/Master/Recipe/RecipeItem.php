<?php

namespace App\Models\Logistic\Master\Recipe;

use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;

class RecipeItem extends Model
{
    protected $fillable = [
        'recipe_id',
        'product_id',
        'quantity',
        'unit_id',
        'cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'cost' => 'decimal:2'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
