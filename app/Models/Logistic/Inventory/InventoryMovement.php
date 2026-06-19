<?php

namespace App\Models\Logistic\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant;
use App\Models\Logistic\Master\Branch\Branch;
use App\Models\Logistic\Master\Warehouse\Warehouse;
use App\Models\Logistic\Master\Product\Product;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'warehouse_id',
        'product_id',
        'reference_type',
        'reference_id',
        'date',
        'qty_in',
        'qty_out',
        'balance_after',
        'notes',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
