<?php

namespace App\Repositories\Logistic\Inventory;

use App\Models\Logistic\Inventory\StockTransfer;
use Illuminate\Database\Eloquent\Builder;

class StockTransferRepository
{
    public function getBaseQuery(int $tenantId): Builder
    {
        return StockTransfer::with([
            'sourceBranch', 'sourceWarehouse', 
            'destinationBranch', 'destinationWarehouse',
            'creator', 'approver', 'poster'
        ])->where('tenant_id', $tenantId);
    }

    public function findByUuid(int $tenantId, string $uuid)
    {
        return StockTransfer::with([
            'sourceBranch', 'sourceWarehouse', 
            'destinationBranch', 'destinationWarehouse',
            'creator', 'approver', 'poster',
            'items.product.unit'
        ])->where('tenant_id', $tenantId)
          ->where('uuid', $uuid)
          ->firstOrFail();
    }

    public function create(array $data): StockTransfer
    {
        return StockTransfer::create($data);
    }

    public function update(StockTransfer $transfer, array $data): bool
    {
        return $transfer->update($data);
    }

    public function delete(StockTransfer $transfer): bool
    {
        return $transfer->delete();
    }
}
