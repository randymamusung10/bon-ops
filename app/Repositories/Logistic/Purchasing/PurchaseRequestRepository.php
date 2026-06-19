<?php

namespace App\Repositories\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\PurchaseRequest;
use Illuminate\Support\Facades\DB;

class PurchaseRequestRepository
{
    public function datatable($tenantId)
    {
        return PurchaseRequest::with(['branch', 'creator', 'approver'])
            ->where('tenant_id', $tenantId)
            ->select('purchase_requests.*');
    }

    public function findByUuid($tenantId, $uuid)
    {
        return PurchaseRequest::with(['branch', 'items.product.unit', 'items.unit', 'creator', 'approver'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function createDraft(array $data, array $items, $tenantId, $userId)
    {
        DB::beginTransaction();
        try {
            $monthYear = date('Ym', strtotime($data['date']));
            $lastPR = PurchaseRequest::where('tenant_id', $tenantId)
                ->where('pr_number', 'like', "PR-{$monthYear}-%")
                ->orderBy('pr_number', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastPR) {
                $lastSeq = (int) substr($lastPR->pr_number, -4);
                $nextNumber = $lastSeq + 1;
            }
            $prNumber = "PR-{$monthYear}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $pr = PurchaseRequest::create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'pr_number' => $prNumber,
                'date' => $data['date'],
                'expected_date' => $data['expected_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $pr->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $pr;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($uuid, $tenantId)
    {
        $pr = PurchaseRequest::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        if ($pr->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $pr->items()->delete();
            $pr->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateDraft($uuid, array $data, array $items, $tenantId, $userId)
    {
        $pr = PurchaseRequest::where('tenant_id', $tenantId)->where('uuid', $uuid)->firstOrFail();
        if ($pr->status !== 'draft') {
            throw new \Exception('Hanya dokumen Draft yang dapat diedit.');
        }

        DB::beginTransaction();
        try {
            $pr->update([
                'branch_id' => $data['branch_id'],
                'date' => $data['date'],
                'expected_date' => $data['expected_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'updated_by' => $userId,
            ]);

            // Hapus item lama, masukkan yang baru
            $pr->items()->delete();
            foreach ($items as $item) {
                $pr->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return $pr;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
