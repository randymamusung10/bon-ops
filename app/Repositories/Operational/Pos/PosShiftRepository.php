<?php

namespace App\Repositories\Operational\Pos;

use App\Models\Operational\Pos\PosShift;
use Illuminate\Database\Eloquent\Builder;

class PosShiftRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return PosShift::with(['branch', 'user'])->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return PosShift::with(['branch', 'user'])->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function getActiveShift(int $tenantId, int $userId)
    {
        return PosShift::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();
    }

    public function create(array $data): PosShift
    {
        return PosShift::create($data);
    }

    public function update(PosShift $shift, array $data): bool
    {
        return $shift->update($data);
    }
}
