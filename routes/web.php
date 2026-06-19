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
            Route::get('company/data', [CompanyController::class, 'data'])->name('company.data');
            Route::get('company/select2', [CompanyController::class, 'select2'])->name('company.select2');
            Route::get('company/create', [CompanyController::class, 'create'])->name('company.create');
            Route::post('company', [CompanyController::class, 'store'])->name('company.store');
            Route::get('company/{uuid}', [CompanyController::class, 'show'])->name('company.show');
            Route::get('company/{uuid}/edit', [CompanyController::class, 'edit'])->name('company.edit');
            Route::put('company/{uuid}', [CompanyController::class, 'update'])->name('company.update');
            Route::delete('company/{uuid}', [CompanyController::class, 'destroy'])->name('company.destroy');
            Route::get('branch', [BranchController::class, 'index'])->name('branch');
            Route::get('branch/data', [BranchController::class, 'data'])->name('branch.data');
            Route::get('branch/select2', [BranchController::class, 'select2'])->name('branch.select2');
            Route::get('branch/create', [BranchController::class, 'create'])->name('branch.create');
            Route::post('branch', [BranchController::class, 'store'])->name('branch.store');
            Route::get('branch/{uuid}', [BranchController::class, 'show'])->name('branch.show');
            Route::get('branch/{uuid}/edit', [BranchController::class, 'edit'])->name('branch.edit');
            Route::put('branch/{uuid}', [BranchController::class, 'update'])->name('branch.update');
            Route::delete('branch/{uuid}', [BranchController::class, 'destroy'])->name('branch.destroy');
            Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse');
            Route::get('customer', [CustomerController::class, 'index'])->name('customer');
            Route::get('customer/data', [CustomerController::class, 'data'])->name('customer.data');
            Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
            Route::post('customer', [CustomerController::class, 'store'])->name('customer.store');
            Route::get('customer/{uuid}', [CustomerController::class, 'show'])->name('customer.show');
            Route::get('customer/{uuid}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
            Route::put('customer/{uuid}', [CustomerController::class, 'update'])->name('customer.update');
            Route::delete('customer/{uuid}', [CustomerController::class, 'destroy'])->name('customer.destroy');
            Route::get('supplier', [SupplierController::class, 'index'])->name('supplier');
            Route::get('supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
            Route::get('supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
            Route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store');
            Route::get('supplier/{uuid}', [SupplierController::class, 'show'])->name('supplier.show');
            Route::get('supplier/{uuid}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
            Route::put('supplier/{uuid}', [SupplierController::class, 'update'])->name('supplier.update');
            Route::delete('supplier/{uuid}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
            Route::get('category', [ProductCategoryController::class, 'index'])->name('category');
            Route::get('category/data', [ProductCategoryController::class, 'data'])->name('category.data');
            Route::get('category/select2', [ProductCategoryController::class, 'select2'])->name('category.select2');
            Route::get('category/create', [ProductCategoryController::class, 'create'])->name('category.create');
            Route::post('category', [ProductCategoryController::class, 'store'])->name('category.store');
            Route::get('category/{uuid}', [ProductCategoryController::class, 'show'])->name('category.show');
            Route::get('category/{uuid}/edit', [ProductCategoryController::class, 'edit'])->name('category.edit');
            Route::put('category/{uuid}', [ProductCategoryController::class, 'update'])->name('category.update');
            Route::delete('category/{uuid}', [ProductCategoryController::class, 'destroy'])->name('category.destroy');
            Route::get('product', [ProductController::class, 'index'])->name('product');
            Route::get('product/data', [ProductController::class, 'data'])->name('product.data');
            Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
            Route::post('product', [ProductController::class, 'store'])->name('product.store');
            Route::get('product/{uuid}', [ProductController::class, 'show'])->name('product.show');
            Route::get('product/{uuid}/edit', [ProductController::class, 'edit'])->name('product.edit');
            Route::put('product/{uuid}', [ProductController::class, 'update'])->name('product.update');
            Route::delete('product/{uuid}', [ProductController::class, 'destroy'])->name('product.destroy');
            Route::get('unit', [UnitController::class, 'index'])->name('unit');
            Route::get('unit/data', [UnitController::class, 'data'])->name('unit.data');
            Route::get('unit/select2', [UnitController::class, 'select2'])->name('unit.select2');
            Route::get('unit/create', [UnitController::class, 'create'])->name('unit.create');
            Route::post('unit', [UnitController::class, 'store'])->name('unit.store');
            Route::get('unit/{uuid}', [UnitController::class, 'show'])->name('unit.show');
            Route::get('unit/{uuid}/edit', [UnitController::class, 'edit'])->name('unit.edit');
            Route::put('unit/{uuid}', [UnitController::class, 'update'])->name('unit.update');
            Route::delete('unit/{uuid}', [UnitController::class, 'destroy'])->name('unit.destroy');
            Route::get('warehouse', [WarehouseController::class, 'index'])->name('warehouse');
            Route::get('warehouse/data', [WarehouseController::class, 'data'])->name('warehouse.data');
            Route::get('warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
            Route::post('warehouse', [WarehouseController::class, 'store'])->name('warehouse.store');
            Route::get('warehouse/{uuid}', [WarehouseController::class, 'show'])->name('warehouse.show');
            Route::get('warehouse/{uuid}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
            Route::put('warehouse/{uuid}', [WarehouseController::class, 'update'])->name('warehouse.update');
            Route::delete('warehouse/{uuid}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');
            Route::get('recipe', [RecipeController::class, 'index'])->name('recipe');
            Route::get('station', [ProductionStationController::class, 'index'])->name('station');
        });
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('balance', [StockBalanceController::class, 'index'])->name('balance');
            Route::get('balance/data', [StockBalanceController::class, 'data'])->name('balance.data');

            Route::get('card', [StockCardController::class, 'index'])->name('card');
            Route::get('card/data', [StockCardController::class, 'data'])->name('card.data');

            Route::get('adjustment', [StockAdjustmentController::class, 'index'])->name('adjustment');
            Route::get('adjustment/data', [StockAdjustmentController::class, 'data'])->name('adjustment.data');
            Route::get('adjustment/create', [StockAdjustmentController::class, 'create'])->name('adjustment.create');
            Route::post('adjustment', [StockAdjustmentController::class, 'store'])->name('adjustment.store');
            Route::get('adjustment/{uuid}', [StockAdjustmentController::class, 'show'])->name('adjustment.show');
            Route::post('adjustment/{uuid}/post', [StockAdjustmentController::class, 'post'])->name('adjustment.post');
            Route::delete('adjustment/{uuid}', [StockAdjustmentController::class, 'destroy'])->name('adjustment.destroy');

            Route::get('transfer', [StockTransferController::class, 'index'])->name('transfer');
            Route::get('transfer/data', [StockTransferController::class, 'data'])->name('transfer.data');
            Route::get('transfer/create', [StockTransferController::class, 'create'])->name('transfer.create');
            Route::post('transfer', [StockTransferController::class, 'store'])->name('transfer.store');
            Route::get('transfer/{uuid}', [StockTransferController::class, 'show'])->name('transfer.show');
            Route::post('transfer/{uuid}/submit', [StockTransferController::class, 'submit'])->name('transfer.submit');
            Route::post('transfer/{uuid}/approve', [StockTransferController::class, 'approve'])->name('transfer.approve');
            Route::post('transfer/{uuid}/post', [StockTransferController::class, 'post'])->name('transfer.post');
            Route::delete('transfer/{uuid}', [StockTransferController::class, 'destroy'])->name('transfer.destroy');
            
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
            // Currency
            Route::get('currency', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'index'])->name('currency');
            Route::get('currency/data', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'data'])->name('currency.data');
            Route::get('currency/create', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'create'])->name('currency.create');
            Route::post('currency', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'store'])->name('currency.store');
            Route::get('currency/{uuid}', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'show'])->name('currency.show');
            Route::get('currency/{uuid}/edit', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'edit'])->name('currency.edit');
            Route::put('currency/{uuid}', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'update'])->name('currency.update');
            Route::delete('currency/{uuid}', [\App\Http\Controllers\Business\Finance\Currency\CurrencyController::class, 'destroy'])->name('currency.destroy');

            // Tax
            Route::get('tax', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'index'])->name('tax');
            Route::get('tax/data', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'data'])->name('tax.data');
            Route::get('tax/select2', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'select2'])->name('tax.select2');
            Route::get('tax/create', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'create'])->name('tax.create');
            Route::post('tax', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'store'])->name('tax.store');
            Route::get('tax/{uuid}', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'show'])->name('tax.show');
            Route::get('tax/{uuid}/edit', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'edit'])->name('tax.edit');
            Route::put('tax/{uuid}', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'update'])->name('tax.update');
            Route::delete('tax/{uuid}', [\App\Http\Controllers\Business\Finance\Tax\TaxController::class, 'destroy'])->name('tax.destroy');

            // COA
            Route::get('coa', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'index'])->name('coa');
            Route::get('coa/data', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'data'])->name('coa.data');
            Route::get('coa/select2', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'select2'])->name('coa.select2');
            Route::get('coa/create', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'create'])->name('coa.create');
            Route::post('coa', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'store'])->name('coa.store');
            Route::get('coa/{uuid}', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'show'])->name('coa.show');
            Route::get('coa/{uuid}/edit', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'edit'])->name('coa.edit');
            Route::put('coa/{uuid}', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'update'])->name('coa.update');
            Route::delete('coa/{uuid}', [\App\Http\Controllers\Business\Finance\ChartOfAccount\ChartOfAccountController::class, 'destroy'])->name('coa.destroy');

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
