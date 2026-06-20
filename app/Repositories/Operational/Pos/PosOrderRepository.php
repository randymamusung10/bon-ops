<?php

namespace App\Repositories\Operational\Pos;

use App\Models\Operational\Pos\PosOrder;
use Illuminate\Database\Eloquent\Builder;

class PosOrderRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return PosOrder::with(['branch', 'posShift', 'creator', 'items.product'])
            ->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return PosOrder::with(['branch', 'posShift', 'creator', 'items.product.unit'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): PosOrder
    {
        return PosOrder::create($data);
    }

    public function update(PosOrder $order, array $data): bool
    {
        return $order->update($data);
    }
}
