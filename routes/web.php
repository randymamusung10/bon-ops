<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business\Crm\CrmCustomer\CrmCustomerController;
use App\Http\Controllers\Business\Crm\CrmLoyalty\CrmLoyaltyController;
use App\Http\Controllers\Business\Crm\CrmMembership\CrmMembershipController;
use App\Http\Controllers\Business\Crm\CrmVoucher\CrmVoucherController;
use App\Http\Controllers\Business\Finance\AccountsPayable\AccountsPayableController;
use App\Http\Controllers\Business\Finance\AccountsReceivable\AccountsReceivableController;
use App\Http\Controllers\Business\Finance\BalanceSheet\BalanceSheetController;
use App\Http\Controllers\Business\Finance\Coa\CoaController;
use App\Http\Controllers\Business\Finance\GeneralJournal\GeneralJournalController;
use App\Http\Controllers\Business\Finance\GeneralLedger\GeneralLedgerController;
use App\Http\Controllers\Business\Finance\ProfitLoss\ProfitLossController;
use App\Http\Controllers\Business\Reports\ExecutiveDashboard\ExecutiveDashboardController;
use App\Http\Controllers\Business\Reports\FoodCostReport\FoodCostReportController;
use App\Http\Controllers\Business\Reports\PurchaseReport\PurchaseReportController;
use App\Http\Controllers\Business\Reports\SalesReport\SalesReportController;
use App\Http\Controllers\Business\Reports\StockReport\StockReportController;
use App\Http\Controllers\Logistic\Inventory\StockAdjustment\StockAdjustmentController;
use App\Http\Controllers\Logistic\Inventory\StockBalance\StockBalanceController;
use App\Http\Controllers\Logistic\Inventory\StockCard\StockCardController;
use App\Http\Controllers\Logistic\Inventory\StockOpname\StockOpnameController;
use App\Http\Controllers\Logistic\Inventory\StockTransfer\StockTransferController;
use App\Http\Controllers\Logistic\Inventory\StockWaste\StockWasteController;
use App\Http\Controllers\Logistic\Master\Branch\BranchController;
use App\Http\Controllers\Logistic\Master\Company\CompanyController;
use App\Http\Controllers\Logistic\Master\Customer\CustomerController;
use App\Http\Controllers\Logistic\Master\ProductCategory\ProductCategoryController;
use App\Http\Controllers\Logistic\Master\Product\ProductController;
use App\Http\Controllers\Logistic\Master\ProductionStation\ProductionStationController;
use App\Http\Controllers\Logistic\Master\Recipe\RecipeController;
use App\Http\Controllers\Logistic\Master\Supplier\SupplierController;
use App\Http\Controllers\Logistic\Master\Unit\UnitController;
use App\Http\Controllers\Logistic\Master\Warehouse\WarehouseController;
use App\Http\Controllers\Logistic\Purchasing\GoodsReceipt\GoodsReceiptController;
use App\Http\Controllers\Logistic\Purchasing\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\Logistic\Purchasing\PurchaseRequisition\PurchaseRequisitionController;
use App\Http\Controllers\Logistic\Purchasing\SupplierInvoice\SupplierInvoiceController;
use App\Http\Controllers\Logistic\Purchasing\SupplierPayment\SupplierPaymentController;
use App\Http\Controllers\Operational\Pos\PosTerminal\PosTerminalController;
use App\Http\Controllers\Operational\Pos\Refund\RefundController;
use App\Http\Controllers\Operational\Pos\SalesHistory\SalesHistoryController;
use App\Http\Controllers\Operational\Pos\Shift\ShiftController;
use App\Http\Controllers\Operational\Restaurant\BaristaDisplay\BaristaDisplayController;
use App\Http\Controllers\Operational\Restaurant\KitchenDisplay\KitchenDisplayController;
use App\Http\Controllers\Operational\Restaurant\OrderQueue\OrderQueueController;
use App\Http\Controllers\Operational\Restaurant\Reservation\ReservationController;
use App\Http\Controllers\Operational\Restaurant\TableManagement\TableManagementController;
use App\Http\Controllers\System\Settings\BranchConfig\BranchConfigController;
use App\Http\Controllers\System\Settings\Permission\PermissionController;
use App\Http\Controllers\System\Settings\Role\RoleController;
use App\Http\Controllers\System\Settings\User\UserController;

// Redirect root ke dashboard (akan diarahkan ke login jika belum auth)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rute untuk Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Rute untuk User Terautentikasi (Auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Operational POS
    Route::prefix('operational')->name('operational.')->group(function () {
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('terminal', [PosTerminalController::class, 'index'])->name('terminal');
            Route::get('history', [SalesHistoryController::class, 'index'])->name('history');
            Route::get('shift', [ShiftController::class, 'index'])->name('shift');
            Route::get('refund', [RefundController::class, 'index'])->name('refund');
        });
        Route::prefix('restaurant')->name('restaurant.')->group(function () {
            Route::get('tables', [TableManagementController::class, 'index'])->name('tables');
            Route::get('reservations', [ReservationController::class, 'index'])->name('reservations');
            Route::get('kitchen', [KitchenDisplayController::class, 'index'])->name('kitchen');
            Route::get('barista', [BaristaDisplayController::class, 'index'])->name('barista');
            Route::get('queue', [OrderQueueController::class, 'index'])->name('queue');
        });
    });

    // Logistic & Master
    Route::prefix('logistic')->name('logistic.')->group(function () {
        Route::prefix('master')->name('master.')->group(function () {
            Route::get('company', [CompanyController::class, 'index'])->name('company');
            Route::get('branch', [BranchController::class, 'index'])->name('branch');
            Route::get('branch/data', [BranchController::class, 'data'])->name('branch.data');
            Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create');
            Route::post('branch', [BranchController::class, 'store'])->name('branch.store');
            Route::get('branch/{uuid}', [BranchController::class, 'show'])->name('branch.show');
            Route::get('branch/{uuid}/edit', [BranchController::class, 'edit'])->name('branch.edit');
            Route::put('branch/{uuid}', [BranchController::class, 'update'])->name('branch.update');
            Route::delete('branch/{uuid}', [BranchController::class, 'destroy'])->name('branch.destroy');
            Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse');
            Route::get('customer', [CustomerController::class, 'index'])->name('customer');
            Route::get('supplier', [SupplierController::class, 'index'])->name('supplier');
            Route::get('category', [ProductCategoryController::class, 'index'])->name('category');
            Route::get('product', [ProductController::class, 'index'])->name('product');
            Route::get('unit', [UnitController::class, 'index'])->name('unit');
            Route::get('recipe', [RecipeController::class, 'index'])->name('recipe');
            Route::get('station', [ProductionStationController::class, 'index'])->name('station');
        });
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('balance', [StockBalanceController::class, 'index'])->name('balance');
            Route::get('card', [StockCardController::class, 'index'])->name('card');
            Route::get('adjustment', [StockAdjustmentController::class, 'index'])->name('adjustment');
            Route::get('transfer', [StockTransferController::class, 'index'])->name('transfer');
            Route::get('opname', [StockOpnameController::class, 'index'])->name('opname');
            Route::get('waste', [StockWasteController::class, 'index'])->name('waste');
        });
        Route::prefix('purchasing')->name('purchasing.')->group(function () {
            Route::get('requisition', [PurchaseRequisitionController::class, 'index'])->name('requisition');
            Route::get('order', [PurchaseOrderController::class, 'index'])->name('order');
            Route::get('receipt', [GoodsReceiptController::class, 'index'])->name('receipt');
            Route::get('invoice', [SupplierInvoiceController::class, 'index'])->name('invoice');
            Route::get('payment', [SupplierPaymentController::class, 'index'])->name('payment');
        });
    });

    // Business
    Route::prefix('business')->name('business.')->group(function () {
        Route::prefix('crm')->name('crm.')->group(function () {
            Route::get('customer', [CrmCustomerController::class, 'index'])->name('customer');
            Route::get('membership', [CrmMembershipController::class, 'index'])->name('membership');
            Route::get('loyalty', [CrmLoyaltyController::class, 'index'])->name('loyalty');
            Route::get('voucher', [CrmVoucherController::class, 'index'])->name('voucher');
        });
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('coa', [CoaController::class, 'index'])->name('coa');
            Route::get('journal', [GeneralJournalController::class, 'index'])->name('journal');
            Route::get('payable', [AccountsPayableController::class, 'index'])->name('payable');
            Route::get('receivable', [AccountsReceivableController::class, 'index'])->name('receivable');
            Route::get('ledger', [GeneralLedgerController::class, 'index'])->name('ledger');
            Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit_loss');
            Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance_sheet');
        });
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('sales', [SalesReportController::class, 'index'])->name('sales');
            Route::get('stock', [StockReportController::class, 'index'])->name('stock');
            Route::get('food-cost', [FoodCostReportController::class, 'index'])->name('food_cost');
            Route::get('purchase', [PurchaseReportController::class, 'index'])->name('purchase');
            Route::get('executive', [ExecutiveDashboardController::class, 'index'])->name('executive');
        });
    });

    // System Settings
    Route::prefix('system')->name('system.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users');
            Route::get('roles', [RoleController::class, 'index'])->name('roles');
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions');
            Route::get('branch-config', [BranchConfigController::class, 'index'])->name('branch_config');
        });
    });
});
