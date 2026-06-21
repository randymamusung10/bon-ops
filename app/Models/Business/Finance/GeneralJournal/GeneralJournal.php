<?php

namespace App\Models\Business\Finance\GeneralJournal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class GeneralJournal extends Model
{
    use SoftDeletes;

    protected $table = 'general_journals';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'branch_id',
        'date',
        'journal_number',
        'reference_type',
        'reference_id',
        'notes',
        'attachment_path',
        'total_debit',
        'total_credit',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (empty($model->updated_by) && auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(GeneralJournalItem::class, 'general_journal_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Logistic\Master\Branch\Branch::class, 'branch_id');
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
