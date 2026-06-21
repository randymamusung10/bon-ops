<?php

namespace App\Models\Business\Finance\GeneralJournal;

use Illuminate\Database\Eloquent\Model;

class GeneralJournalItem extends Model
{
    protected $table = 'general_journal_items';

    protected $fillable = [
        'general_journal_id',
        'chart_of_account_id',
        'debit',
        'credit',
        'description',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal()
    {
        return $this->belongsTo(GeneralJournal::class, 'general_journal_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Business\Finance\ChartOfAccount\ChartOfAccount::class, 'chart_of_account_id');
    }
}
