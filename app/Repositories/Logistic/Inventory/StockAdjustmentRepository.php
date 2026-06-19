<?php

namespace App\Repositories\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockAdjustment;
use Illuminate\Database\Eloquent\Builder;

class StockAdjustmentRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return StockAdjustment::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster'
        ])->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return StockAdjustment::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster',
            'items.product.unit'
        ])->where('tenant_id', $tenantId)
          ->where('uuid', $uuid)
          ->firstOrFail();
    }

    public function create(array $data): StockAdjustment
    {
        return StockAdjustment::create($data);
    }

    public function update(StockAdjustment $adjustment, array $data): bool
    {
        return $adjustment->update($data);
    }

    public function updateStatus(StockAdjustment $adjustment, string $status, array $additionalData = []): bool
    {
        $data = array_merge(['status' => $status], $additionalData);
        return $adjustment->update($data);
    }

    public function delete(StockAdjustment $adjustment): bool
    {
        // Also delete items if necessary or rely on cascade/soft deletes
        $adjustment->items()->delete();
        return $adjustment->delete();
    }
}
