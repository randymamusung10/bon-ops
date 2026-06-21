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

            // Check if there are any unpaid daily kasbon (no due_date)
            $unpaidDailyOrders = PosOrder::where('pos_shift_id', $shift->id)
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->whereNull('due_date')
                ->count();

            if ($unpaidDailyOrders > 0) {
                throw new Exception("Terdapat {$unpaidDailyOrders} transaksi kasbon harian yang belum dilunasi. Harap lunasi terlebih dahulu sebelum menutup shift.");
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
