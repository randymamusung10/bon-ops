<?php

namespace App\Models\Logistic\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'system_qty',
        'actual_qty',
        'difference',
        'reason',
    ];

    public function stockAdjustment() { return $this->belongsTo(StockAdjustment::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
