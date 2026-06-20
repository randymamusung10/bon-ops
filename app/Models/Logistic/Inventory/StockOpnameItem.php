<?php

namespace App\Models\Logistic\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'system_qty',
        'actual_qty',
        'difference',
        'notes',
    ];

    public function stockOpname() { return $this->belongsTo(StockOpname::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
