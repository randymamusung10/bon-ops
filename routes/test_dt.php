<?php
use Illuminate\Support\Facades\Route;
use App\Models\Business\Finance\GeneralLedger\GeneralLedger;

Route::get('/test-dt', function() {
    $query = GeneralLedger::with(['account', 'source'])->where('tenant_id', 1)->select('general_ledgers.*');
    return yajra\DataTables\DataTables::of($query)->make(true);
});
