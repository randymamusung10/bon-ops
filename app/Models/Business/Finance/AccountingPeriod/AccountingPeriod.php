<?php

namespace App\Models\Business\Finance\AccountingPeriod;

use App\Models\System\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingPeriod extends Model
{
    protected $fillable = [
        'tenant_id',
        'month',
        'year',
        'status',
        'closed_by',
        'closed_at'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Check if a specific date is within an open period.
     */
    public static function isOpen($tenantId, \Carbon\Carbon $date): bool
    {
        $period = self::where('tenant_id', $tenantId)
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->first();

        // If not found, usually it's considered open unless strict closing is required, 
        // but typically standard is to allow open unless explicitly closed.
        if (!$period) {
            return true;
        }

        return $period->status === 'open';
    }
}
