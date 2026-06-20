<?php

namespace App\Services\Operational\Pos;

use App\Models\Operational\Pos\PosShift;
use App\Models\Operational\Pos\PosOrder;
use App\Repositories\Operational\Pos\PosShiftRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PosShiftService
{
    protected $repository;

    public function __construct(PosShiftRepository $repository)
    {
        $this->repository = $repository;
    }

    public function openShift(array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $userId = Auth::id();

            // Check if there is an active open shift
            $activeShift = $this->repository->getActiveShift($tenantId, $userId);
            if ($activeShift) {
                throw new Exception("Anda memiliki shift kasir yang masih aktif/belum ditutup.");
            }

            $shift = $this->repository->create([
                'tenant_id' => $tenantId,
                'branch_id' => Auth::user()->branch_id ?? $data['branch_id'],
                'user_id' => $userId,
                'start_time' => now(),
                'start_cash' => $data['start_cash'],
                'status' => 'open',
            ]);

            DB::commit();
            return $shift;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function closeShift(string $uuid, array $data)
    {
        try {
            DB::beginTransaction();

            $tenantId = Auth::user()->tenant_id ?? 1;
            $shift = $this->repository->findByUuid($tenantId, $uuid);

            if ($shift->status !== 'open') {
                throw new Exception("Shift kasir sudah ditutup sebelumnya.");
            }

            // Check if there are still active/unfinished order items in the kitchen or barista display
            $unfinishedItemsCount = \App\Models\Operational\Pos\PosOrderItem::whereHas('posOrder', function($q) use ($shift) {
                $q->where('pos_shift_id', $shift->id);
            })->where('status', '!=', 'completed')->count();

            if ($unfinishedItemsCount > 0) {
                throw new Exception("Tidak dapat menutup shift. Masih ada {$unfinishedItemsCount} item pesanan yang belum selesai diproses di dapur/barista.");
            }

            // Calculate expected cash in drawer
            $cashOrdersTotal = PosOrder::where('pos_shift_id', $shift->id)
                ->where('payment_status', 'paid')
                ->where('payment_method', 'cash')
                ->sum('grand_total');

            $expectedEndCash = $shift->start_cash + $cashOrdersTotal;

            $this->repository->update($shift, [
                'end_time' => now(),
                'end_cash' => $expectedEndCash,
                'actual_end_cash' => $data['actual_end_cash'],
                'notes' => $data['notes'] ?? null,
                'status' => 'closed',
            ]);

            DB::commit();
            return $shift;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
