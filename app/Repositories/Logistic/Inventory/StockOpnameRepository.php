<?php

namespace App\Repositories\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockOpname;
use Illuminate\Database\Eloquent\Builder;

class StockOpnameRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return StockOpname::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster'
        ])->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return StockOpname::with([
            'branch', 'warehouse', 
            'creator', 'submitter', 'approver', 'poster',
            'items.product.unit'
        ])->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function create(array $data): StockOpname
    {
        return StockOpname::create($data);
    }

    public function update(StockOpname $opname, array $data): bool
    {
        return $opname->update($data);
    }

    public function updateStatus(StockOpname $opname, string $status, array $additionalData = []): bool
    {
        $data = array_merge(['status' => $status], $additionalData);
        return $opname->update($data);
    }

    public function delete(StockOpname $opname): bool
    {
        $opname->items()->delete();
        return $opname->delete();
    }
}
