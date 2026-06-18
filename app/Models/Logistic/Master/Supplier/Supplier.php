<?php

namespace App\Models\Logistic\Master\Supplier;

use App\Models\Logistic\Master\BaseMasterModel;
use App\Models\MasterData\Currency;
use App\Models\MasterData\Tax;
use App\Models\MasterData\ChartOfAccount;

class Supplier extends BaseMasterModel
{
    protected $fillable = [
        'tenant_id', 'company_id', 'uuid', 'code', 'name',
        'contact_person_name', 'contact_person_phone', 'email', 'phone',
        'address', 'city', 'tax_number', 
        'account_payable_id', 'default_currency_id', 'tax_id',
        'status', 'created_by', 'updated_by'
    ];
    
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }
    
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    
    public function accountPayable()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_payable_id');
    }
}
