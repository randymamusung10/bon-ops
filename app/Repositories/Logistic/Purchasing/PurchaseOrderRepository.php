<?php

namespace App\Repositories\Logistic\Purchasing;

use App\Models\Logistic\Purchasing\PurchaseOrder;
use App\Models\Logistic\Purchasing\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository
{
    /**
     * Get base query for datatables
     */
    public function getBaseQuery($tenantId)
    {
        return PurchaseOrder::with(['branch', 'supplier', 'creator'])
            ->where('tenant_id', $tenantId)
            ->latest('date')
            ->latest('id');
    }

    /**
     * Find PO by UUID
     */
    public function findByUuid($tenantId, $uuid)
    {
        return PurchaseOrder::with(['branch', 'supplier', 'items.product', 'items.unit', 'creator', 'updater', 'approver'])
            ->where('tenant_id', $tenantId)
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * Create Draft PO
     */
    public function createDraft(array $data, array $items, $tenantId, $userId)
    {
        DB::beginTransaction();
        try {
            // Generate PO Number
            $poNumber = $this->generatePoNumber($tenantId, $data['date']);

            $po = PurchaseOrder::create([
                'tenant_id' => $tenantId,
                'branch_id' => $data['branch_id'],
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'expected_date' => $data['expected_date'] ?? null,
                'po_number' => $poNumber,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                $quantity = floatval($item['quantity']);
                $unitPrice = floatval($item['unit_price']);
                $totalPrice = $quantity * $unitPrice;
                $totalAmount += $totalPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);

            DB::commit();
            return $po;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete Draft PO
     */
    public function delete($uuid, $tenantId)
    {
        $po = $this->findByUuid($tenantId, $uuid);
        
        if ($po->status !== 'draft') {
            throw new \Exception('Hanya dokumen dengan status Draft yang dapat dihapus.');
        }

        return $po->delete();
    }

    /**
     * Generate PO Number: PO-YYMMDD-XXXX
     */
    private function generatePoNumber($tenantId, $date)
    {
        $prefix = 'PO-';
        $dateStr = \Carbon\Carbon::parse($date)->format('ymd');
        
        $lastPo = PurchaseOrder::where('tenant_id', $tenantId)
            ->whereDate('date', $date)
            ->where('po_number', 'like', $prefix . $dateStr . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastPo) {
            $lastSequence = intval(substr($lastPo->po_number, -4));
            $sequence = $lastSequence + 1;
        }

        return $prefix . $dateStr . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
