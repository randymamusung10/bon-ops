<?php

namespace App\Models\Business\Finance\CashBank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CashTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Logistic\Master\Branch\Branch::class);
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'account_id');
    }

    public function items()
    {
        return $this->hasMany(CashTransactionItem::class, 'cash_transaction_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
