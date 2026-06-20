<?php

namespace App\Models\Operational\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;

class PosOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'product_id',
        'qty',
        'price',
        'tax_amount',
        'discount_amount',
        'subtotal',
        'status',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'price' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'subtotal' => 'decimal:4',
    ];

    public function posOrder() { return $this->belongsTo(PosOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
