<?php

namespace App\Models\Business\Crm;

use Illuminate\Database\Eloquent\Model;

class CrmMembership extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'crm_memberships';

    protected $fillable = [
        'tenant_id',
        'name',
        'minimum_spend',
        'discount_percentage',
        'status',
    ];

    public function customers()
    {
        return $this->hasMany(\App\Models\Logistic\Master\Customer\Customer::class, 'membership_id');
    }
}
