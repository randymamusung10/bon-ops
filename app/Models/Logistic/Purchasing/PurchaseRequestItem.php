<?php

namespace App\Models\Logistic\Purchasing;

use Illuminate\Database\Eloquent\Model;
use App\Models\Logistic\Master\Product\Product;
use App\Models\Logistic\Master\Unit\Unit;

class PurchaseRequestItem extends Model
{
    protected $table = 'purchase_request_items';
    protected $guarded = ['id'];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
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
