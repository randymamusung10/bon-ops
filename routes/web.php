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
use App\Http\Controllers\Logistic\Purchasing\PurchaseRequest\PurchaseRequestController;
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
            Route::post('terminal', [PosTerminalController::class, 'store'])->name('terminal.store');
            Route::get('terminal/unpaid', [PosTerminalController::class, 'unpaidOrders'])->name('terminal.unpaid');
            Route::get('terminal/order/{uuid}', [PosTerminalController::class, 'orderDetail'])->name('terminal.order');
            Route::get('terminal/{uuid}/receipt', [PosTerminalController::class, 'receipt'])->name('terminal.receipt');
            Route::get('history', [SalesHistoryController::class, 'index'])->name('history');
            Route::get('history/data', [SalesHistoryController::class, 'data'])->name('history.data');
            Route::get('history/{uuid}', [SalesHistoryController::class, 'detail'])->name('history.detail');
            
            Route::get('shift', [ShiftController::class, 'index'])->name('shift');
            Route::get('shift/data', [ShiftController::class, 'data'])->name('shift.data');
            Route::get('shift/summary/{uuid}', [ShiftController::class, 'summary'])->name('shift.summary');
            Route::get('shift/detail/{uuid}', [ShiftController::class, 'detail'])->name('shift.detail');
            Route::post('shift/open', [ShiftController::class, 'open'])->name('shift.open');
            Route::post('shift/close/{uuid}', [ShiftController::class, 'close'])->name('shift.close');
            
            Route::get('refund', [RefundController::class, 'index'])->name('refund');
            Route::get('refund/search', [RefundController::class, 'search'])->name('refund.search');
            Route::get('refund/autocomplete', [RefundController::class, 'autocomplete'])->name('refund.autocomplete');
            Route::post('refund/process/{uuid}', [RefundController::class, 'process'])->name('refund.process');
        });
        Route::prefix('restaurant')->name('restaurant.')->group(function () {
            Route::get('tables', [TableManagementController::class, 'index'])->name('tables');
            Route::get('reservations', [ReservationController::class, 'index'])->name('reservations');
            
            Route::get('kitchen', [KitchenDisplayController::class, 'index'])->name('kitchen');
            Route::post('kitchen/{id}/status', [KitchenDisplayController::class, 'updateStatus'])->name('kitchen.status');
            
            Route::get('barista', [BaristaDisplayController::class, 'index'])->name('barista');
            Route::post('barista/{id}/status', [BaristaDisplayController::class, 'updateStatus'])->name('barista.status');
            
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
            Route::get('recipe/data', [RecipeController::class, 'data'])->name('recipe.data');
            Route::get('recipe/create', [RecipeController::class, 'create'])->name('recipe.create');
            Route::post('recipe', [RecipeController::class, 'store'])->name('recipe.store');
            Route::get('recipe/{uuid}', [RecipeController::class, 'show'])->name('recipe.show');
            Route::get('recipe/{uuid}/edit', [RecipeController::class, 'edit'])->name('recipe.edit');
            Route::put('recipe/{uuid}', [RecipeController::class, 'update'])->name('recipe.update');
            Route::delete('recipe/{uuid}', [RecipeController::class, 'destroy'])->name('recipe.destroy');

            Route::get('station', [ProductionStationController::class, 'index'])->name('station');
            Route::get('station/data', [ProductionStationController::class, 'data'])->name('station.data');
            Route::get('station/select2', [ProductionStationController::class, 'select2'])->name('station.select2');
            Route::get('station/create', [ProductionStationController::class, 'create'])->name('station.create');
            Route::post('station', [ProductionStationController::class, 'store'])->name('station.store');
            Route::get('station/{uuid}/edit', [ProductionStationController::class, 'edit'])->name('station.edit');
            Route::put('station/{uuid}', [ProductionStationController::class, 'update'])->name('station.update');
            Route::delete('station/{uuid}', [ProductionStationController::class, 'destroy'])->name('station.destroy');
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
            Route::get('adjustment/{uuid}/edit', [StockAdjustmentController::class, 'edit'])->name('adjustment.edit');
            Route::put('adjustment/{uuid}', [StockAdjustmentController::class, 'update'])->name('adjustment.update');
            Route::post('adjustment/{uuid}/submit', [StockAdjustmentController::class, 'submit'])->name('adjustment.submit');
            Route::post('adjustment/{uuid}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustment.approve');
            Route::post('adjustment/{uuid}/post', [StockAdjustmentController::class, 'post'])->name('adjustment.post');
            Route::delete('adjustment/{uuid}', [StockAdjustmentController::class, 'destroy'])->name('adjustment.destroy');

            Route::get('transfer', [StockTransferController::class, 'index'])->name('transfer');
            Route::get('transfer/data', [StockTransferController::class, 'data'])->name('transfer.data');
            Route::get('transfer/create', [StockTransferController::class, 'create'])->name('transfer.create');
            Route::post('transfer', [StockTransferController::class, 'store'])->name('transfer.store');
            Route::get('transfer/{uuid}', [StockTransferController::class, 'show'])->name('transfer.show');
            Route::get('transfer/{uuid}/edit', [StockTransferController::class, 'edit'])->name('transfer.edit');
            Route::put('transfer/{uuid}', [StockTransferController::class, 'update'])->name('transfer.update');
            Route::post('transfer/{uuid}/submit', [StockTransferController::class, 'submit'])->name('transfer.submit');
            Route::post('transfer/{uuid}/approve', [StockTransferController::class, 'approve'])->name('transfer.approve');
            Route::post('transfer/{uuid}/post', [StockTransferController::class, 'post'])->name('transfer.post');
            Route::delete('transfer/{uuid}', [StockTransferController::class, 'destroy'])->name('transfer.destroy');
            
             Route::get('opname', [StockOpnameController::class, 'index'])->name('opname');
             Route::get('opname/data', [StockOpnameController::class, 'data'])->name('opname.data');
             Route::get('opname/system-stock', [StockOpnameController::class, 'getSystemStock'])->name('opname.system_stock');
             Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
             Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store');
             Route::get('opname/{uuid}', [StockOpnameController::class, 'show'])->name('opname.show');
             Route::get('opname/{uuid}/edit', [StockOpnameController::class, 'edit'])->name('opname.edit');
             Route::put('opname/{uuid}', [StockOpnameController::class, 'update'])->name('opname.update');
             Route::post('opname/{uuid}/submit', [StockOpnameController::class, 'submit'])->name('opname.submit');
             Route::post('opname/{uuid}/approve', [StockOpnameController::class, 'approve'])->name('opname.approve');
             Route::post('opname/{uuid}/post', [StockOpnameController::class, 'post'])->name('opname.post');
             Route::delete('opname/{uuid}', [StockOpnameController::class, 'destroy'])->name('opname.destroy');
             
             Route::get('waste', [StockWasteController::class, 'index'])->name('waste');
             Route::get('waste/data', [StockWasteController::class, 'data'])->name('waste.data');
             Route::get('waste/create', [StockWasteController::class, 'create'])->name('waste.create');
             Route::post('waste', [StockWasteController::class, 'store'])->name('waste.store');
             Route::get('waste/{uuid}', [StockWasteController::class, 'show'])->name('waste.show');
             Route::get('waste/{uuid}/edit', [StockWasteController::class, 'edit'])->name('waste.edit');
             Route::put('waste/{uuid}', [StockWasteController::class, 'update'])->name('waste.update');
             Route::post('waste/{uuid}/submit', [StockWasteController::class, 'submit'])->name('waste.submit');
             Route::post('waste/{uuid}/approve', [StockWasteController::class, 'approve'])->name('waste.approve');
             Route::post('waste/{uuid}/post', [StockWasteController::class, 'post'])->name('waste.post');
             Route::delete('waste/{uuid}', [StockWasteController::class, 'destroy'])->name('waste.destroy');
         });
        Route::prefix('purchasing')->name('purchasing.')->group(function () {
            Route::get('request', [PurchaseRequestController::class, 'index'])->name('request');
            Route::get('request/data', [PurchaseRequestController::class, 'data'])->name('request.data');
            Route::get('request/create', [PurchaseRequestController::class, 'create'])->name('request.create');
            Route::post('request', [PurchaseRequestController::class, 'store'])->name('request.store');
            Route::get('request/{uuid}', [PurchaseRequestController::class, 'show'])->name('request.show');
            Route::get('request/{uuid}/edit', [PurchaseRequestController::class, 'edit'])->name('request.edit');
            Route::put('request/{uuid}', [PurchaseRequestController::class, 'update'])->name('request.update');
            Route::delete('request/{uuid}', [PurchaseRequestController::class, 'destroy'])->name('request.destroy');
            Route::post('request/{uuid}/submit', [PurchaseRequestController::class, 'submit'])->name('request.submit');
            Route::post('request/{uuid}/approve', [PurchaseRequestController::class, 'approve'])->name('request.approve');
            Route::post('request/{uuid}/post', [PurchaseRequestController::class, 'post'])->name('request.post');

            Route::get('order', [PurchaseOrderController::class, 'index'])->name('order');
            Route::get('order/data', [PurchaseOrderController::class, 'data'])->name('order.data');
            Route::get('order/create', [PurchaseOrderController::class, 'create'])->name('order.create');
            Route::get('order/get-pr/{uuid}', [PurchaseOrderController::class, 'getPurchaseRequestDetails'])->name('order.get_pr');
            Route::post('order', [PurchaseOrderController::class, 'store'])->name('order.store');
            Route::get('order/{uuid}', [PurchaseOrderController::class, 'show'])->name('order.show');
            Route::get('order/{uuid}/edit', [PurchaseOrderController::class, 'edit'])->name('order.edit');
            Route::put('order/{uuid}', [PurchaseOrderController::class, 'update'])->name('order.update');
            Route::post('order/{uuid}/submit', [PurchaseOrderController::class, 'submit'])->name('order.submit');
            Route::post('order/{uuid}/approve', [PurchaseOrderController::class, 'approve'])->name('order.approve');
            Route::post('order/{uuid}/post', [PurchaseOrderController::class, 'post'])->name('order.post');
            Route::delete('order/{uuid}', [PurchaseOrderController::class, 'destroy'])->name('order.destroy');
            Route::get('receipt', [GoodsReceiptController::class, 'index'])->name('receipt');
            Route::get('receipt/data', [GoodsReceiptController::class, 'data'])->name('receipt.data');
            Route::get('receipt/create', [GoodsReceiptController::class, 'create'])->name('receipt.create');
            Route::get('receipt/get-po/{id}', [GoodsReceiptController::class, 'getPoDetails'])->name('receipt.get_po');
            Route::post('receipt', [GoodsReceiptController::class, 'store'])->name('receipt.store');
            Route::get('receipt/{uuid}', [GoodsReceiptController::class, 'show'])->name('receipt.show');
            Route::get('receipt/{uuid}/edit', [GoodsReceiptController::class, 'edit'])->name('receipt.edit');
            Route::put('receipt/{uuid}', [GoodsReceiptController::class, 'update'])->name('receipt.update');
            Route::post('receipt/{uuid}/submit', [GoodsReceiptController::class, 'submit'])->name('receipt.submit');
            Route::post('receipt/{uuid}/approve', [GoodsReceiptController::class, 'approve'])->name('receipt.approve');
            Route::post('receipt/{uuid}/post', [GoodsReceiptController::class, 'post'])->name('receipt.post');
            Route::delete('receipt/{uuid}', [GoodsReceiptController::class, 'destroy'])->name('receipt.destroy');
            Route::get('invoice', [SupplierInvoiceController::class, 'index'])->name('invoice');
            Route::get('invoice/data', [SupplierInvoiceController::class, 'data'])->name('invoice.data');
            Route::get('invoice/create', [SupplierInvoiceController::class, 'create'])->name('invoice.create');
            Route::get('invoice/get-gr/{id}', [SupplierInvoiceController::class, 'getGrDetails'])->name('invoice.get-gr');
            Route::post('invoice', [SupplierInvoiceController::class, 'store'])->name('invoice.store');
            Route::get('invoice/{uuid}', [SupplierInvoiceController::class, 'show'])->name('invoice.show');
            Route::get('invoice/{uuid}/edit', [SupplierInvoiceController::class, 'edit'])->name('invoice.edit');
            Route::put('invoice/{uuid}', [SupplierInvoiceController::class, 'update'])->name('invoice.update');
            Route::post('invoice/{uuid}/submit', [SupplierInvoiceController::class, 'submit'])->name('invoice.submit');
            Route::post('invoice/{uuid}/approve', [SupplierInvoiceController::class, 'approve'])->name('invoice.approve');
            Route::post('invoice/{uuid}/post', [SupplierInvoiceController::class, 'post'])->name('invoice.post');
            Route::delete('invoice/{uuid}', [SupplierInvoiceController::class, 'destroy'])->name('invoice.destroy');
            Route::get('payment', [SupplierPaymentController::class, 'index'])->name('payment');
            Route::get('payment/data', [SupplierPaymentController::class, 'data'])->name('payment.data');
            Route::get('payment/create', [SupplierPaymentController::class, 'create'])->name('payment.create');
            Route::post('payment', [SupplierPaymentController::class, 'store'])->name('payment.store');
            Route::get('payment/{uuid}', [SupplierPaymentController::class, 'show'])->name('payment.show');
            Route::get('payment/{uuid}/edit', [SupplierPaymentController::class, 'edit'])->name('payment.edit');
            Route::put('payment/{uuid}', [SupplierPaymentController::class, 'update'])->name('payment.update');
            Route::post('payment/{uuid}/submit', [SupplierPaymentController::class, 'submit'])->name('payment.submit');
            Route::post('payment/{uuid}/approve', [SupplierPaymentController::class, 'approve'])->name('payment.approve');
            Route::post('payment/{uuid}/post', [SupplierPaymentController::class, 'post'])->name('payment.post');
            Route::delete('payment/{uuid}', [SupplierPaymentController::class, 'destroy'])->name('payment.destroy');
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
            Route::get('journal/data', [GeneralJournalController::class, 'data'])->name('journal.data');
            Route::get('journal/references', [GeneralJournalController::class, 'getReferences'])->name('journal.references');
            Route::get('journal/reference-details', [GeneralJournalController::class, 'getReferenceDetails'])->name('journal.reference_details');
            Route::get('journal/create', [GeneralJournalController::class, 'create'])->name('journal.create');
            Route::post('journal', [GeneralJournalController::class, 'store'])->name('journal.store');
            Route::get('journal/{uuid}', [GeneralJournalController::class, 'show'])->name('journal.show');
            Route::get('journal/{uuid}/edit', [GeneralJournalController::class, 'edit'])->name('journal.edit');
            Route::put('journal/{uuid}', [GeneralJournalController::class, 'update'])->name('journal.update');
            Route::delete('journal/{uuid}', [GeneralJournalController::class, 'destroy'])->name('journal.destroy');
            Route::post('journal/{uuid}/submit', [GeneralJournalController::class, 'submit'])->name('journal.submit');
            Route::post('journal/{uuid}/approve', [GeneralJournalController::class, 'approve'])->name('journal.approve');
            Route::post('journal/{uuid}/post', [GeneralJournalController::class, 'post'])->name('journal.post');
            Route::get('journal/{uuid}/print', [GeneralJournalController::class, 'printVoucher'])->name('journal.print');

            // Cash & Bank
            Route::prefix('cash-receipt')->name('cash_receipt.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'index'])->name('index');
                Route::get('data', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'data'])->name('data');
                Route::get('create', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'store'])->name('store');
                Route::get('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'show'])->name('show');
                Route::get('{uuid}/edit', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'edit'])->name('edit');
                Route::put('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'update'])->name('update');
                Route::delete('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'destroy'])->name('destroy');
                Route::post('{uuid}/submit', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'submit'])->name('submit');
                Route::post('{uuid}/approve', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'approve'])->name('approve');
                Route::post('{uuid}/post', [\App\Http\Controllers\Business\Finance\CashBank\CashReceiptController::class, 'post'])->name('post');
            });
            
            Route::prefix('cash-disbursement')->name('cash_disbursement.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'index'])->name('index');
                Route::get('data', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'data'])->name('data');
                Route::get('create', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'store'])->name('store');
                Route::get('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'show'])->name('show');
                Route::get('{uuid}/edit', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'edit'])->name('edit');
                Route::put('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'update'])->name('update');
                Route::delete('{uuid}', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'destroy'])->name('destroy');
                Route::post('{uuid}/submit', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'submit'])->name('submit');
                Route::post('{uuid}/approve', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'approve'])->name('approve');
                Route::post('{uuid}/post', [\App\Http\Controllers\Business\Finance\CashBank\CashDisbursementController::class, 'post'])->name('post');
            });

            Route::get('payable', [AccountsPayableController::class, 'index'])->name('payable');
            Route::get('payable/data', [AccountsPayableController::class, 'data'])->name('payable.data');
            Route::get('payable/{uuid}/show-modal', [AccountsPayableController::class, 'showModal'])->name('payable.show_modal');
            Route::get('receivable', [AccountsReceivableController::class, 'index'])->name('receivable');
            Route::get('receivable/data', [AccountsReceivableController::class, 'data'])->name('receivable.data');
            Route::get('receivable/{uuid}/payment-modal', [AccountsReceivableController::class, 'paymentModal'])->name('receivable.payment_modal');
            Route::get('receivable/{uuid}/show-modal', [AccountsReceivableController::class, 'showModal'])->name('receivable.show_modal');
            Route::post('receivable/{uuid}/pay', [AccountsReceivableController::class, 'pay'])->name('receivable.pay');
            Route::get('ledger', [GeneralLedgerController::class, 'index'])->name('ledger');
            Route::get('ledger/data', [GeneralLedgerController::class, 'data'])->name('ledger.data');
            Route::get('ledger/summary', [GeneralLedgerController::class, 'summary'])->name('ledger.summary');
            Route::get('ledger/print', [GeneralLedgerController::class, 'print'])->name('ledger.print');
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
            // Users
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/data', [UserController::class, 'data'])->name('users.data');
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            // Roles
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
            Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions');
            Route::get('branch-config', [BranchConfigController::class, 'index'])->name('branch_config');
        });
    });
});
require __DIR__.'/test_dt.php';
