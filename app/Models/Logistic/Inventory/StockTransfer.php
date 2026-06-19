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

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'source_branch_id',
        'source_warehouse_id',
        'destination_branch_id',
        'destination_warehouse_id',
        'uuid',
        'document_number',
        'date',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'date' => 'date',
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
    public function sourceBranch() { return $this->belongsTo(Branch::class, 'source_branch_id'); }
    public function sourceWarehouse() { return $this->belongsTo(Warehouse::class, 'source_warehouse_id'); }
    public function destinationBranch() { return $this->belongsTo(Branch::class, 'destination_branch_id'); }
    public function destinationWarehouse() { return $this->belongsTo(Warehouse::class, 'destination_warehouse_id'); }
    public function items() { return $this->hasMany(StockTransferItem::class); }
    
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function poster() { return $this->belongsTo(User::class, 'posted_by'); }
}
