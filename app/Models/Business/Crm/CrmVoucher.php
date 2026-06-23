<?php

namespace App\Models\Business\Crm;

use Illuminate\Database\Eloquent\Model;

class CrmVoucher extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'crm_vouchers';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'value',
        'minimum_spend',
        'maximum_discount',
        'quota',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
    ];
}
