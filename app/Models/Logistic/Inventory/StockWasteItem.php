<?php

namespace App\Models\Logistic\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;

class StockWasteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_waste_id',
        'product_id',
        'qty',
        'reason',
        'cost',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'cost' => 'decimal:4',
    ];

    public function stockWaste() { return $this->belongsTo(StockWaste::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
