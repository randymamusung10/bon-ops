<?php

namespace App\Models\Operational\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'payment_date',
        'amount',
        'payment_method',
        'attachment_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:4',
    ];

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
