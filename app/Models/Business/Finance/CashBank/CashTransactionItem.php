<?php

namespace App\Models\Business\Finance\CashBank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CashTransactionItem extends Model
{
    use HasFactory;

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

    public function transaction()
    {
        return $this->belongsTo(CashTransaction::class, 'cash_transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'account_id');
    }
}
