<?php

namespace App\Models\Operational\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\User;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'uuid',
        'start_time',
        'end_time',
        'start_cash',
        'end_cash',
        'actual_end_cash',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_cash' => 'decimal:4',
        'end_cash' => 'decimal:4',
        'actual_end_cash' => 'decimal:4',
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
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function orders() { return $this->hasMany(PosOrder::class); }
}
