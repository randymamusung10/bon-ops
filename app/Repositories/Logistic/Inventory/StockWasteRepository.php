<?php

namespace App\Repositories\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockWaste;
use Illuminate\Database\Eloquent\Builder;

class StockWasteRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return StockWaste::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster'
        ])->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return StockWaste::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster',
            'items.product.unit'
        ])->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): StockWaste
    {
        return StockWaste::create($data);
    }

    public function update(StockWaste $waste, array $data): bool
    {
        return $waste->update($data);
    }

    public function updateStatus(StockWaste $waste, string $status, array $additionalData = []): bool
    {
        $data = array_merge(['status' => $status], $additionalData);
        return $waste->update($data);
    }

    public function delete(StockWaste $waste): bool
    {
        $waste->items()->delete();
        return $waste->delete();
    }
}
