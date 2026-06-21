<?php

namespace App\Models\Business\Finance\GeneralLedger;

use App\Models\System\Branch;
use App\Models\System\Tenant;
use App\Models\Business\Finance\ChartOfAccount\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeneralLedger extends Model
{
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'date',
        'source_type',
        'source_id',
        'chart_of_account_id',
        'description',
        'debit',
        'credit'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    /**
     * Get the parent source model (e.g. GeneralJournal, PurchaseOrder).
     */
    public function source()
    {
        return $this->morphTo(null, 'source_type', 'source_id');
    }
}
