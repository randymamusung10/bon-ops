<?php

namespace App\Models\Business\Crm;

use Illuminate\Database\Eloquent\Model;

class CrmLoyaltyPoint extends Model
{
    protected $table = 'crm_loyalty_points';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'points',
        'reference_type',
        'reference_id',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Logistic\Master\Customer\Customer::class, 'customer_id');
    }
}
