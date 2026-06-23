<?php

namespace App\Models\Logistic\Master\Customer;

use App\Models\Logistic\Master\BaseMasterModel;
use App\Models\Business\Finance\Currency\Currency;
use App\Models\Business\Finance\Tax\Tax;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;

class Customer extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'contact_person_name', 'contact_person_phone', 'email', 'phone',
        'address', 'city', 'credit_limit',
        'account_receivable_id', 'default_currency_id', 'tax_id',
        'status', 'membership_id', 'loyalty_points_balance', 'created_by', 'updated_by'
    ];
    
    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    
    public function accountReceivable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_receivable_id');
    }
}
