<?php

namespace App\Models\Operational\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\User;

class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'pos_shift_id',
        'uuid',
        'order_number',
        'date',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'customer_name',
        'table_number',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'grand_total' => 'decimal:4',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function posShift() { return $this->belongsTo(PosShift::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(PosOrderItem::class); }
}
