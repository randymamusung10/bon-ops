<?php

namespace App\Models\Logistic\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\User;

class StockWaste extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'warehouse_id',
        'uuid',
        'document_number',
        'date',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
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
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(StockWasteItem::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function poster() { return $this->belongsTo(User::class, 'posted_by'); }
}
