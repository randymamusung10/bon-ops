<?php

namespace App\Models\Logistic\Purchasing;

use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;

class GoodsReceiptItem extends Model
{
    protected $table = 'goods_receipt_items';
    protected $guarded = ['id'];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
