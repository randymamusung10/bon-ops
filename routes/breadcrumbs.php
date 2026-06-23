<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard'));
});

// Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

// Sistem > Manajemen User
Breadcrumbs::for('system.settings.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sistem', '#');
    $trail->push('Manajemen User', route('system.settings.users.index'));
});

// Sistem > Role & Jabatan
Breadcrumbs::for('system.settings.roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sistem', '#');
    $trail->push('Role & Jabatan', route('system.settings.roles.index'));
});

// Master Data > Perusahaan
Breadcrumbs::for('logistic.master.company', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Perusahaan', route('logistic.master.company'));
});

// Master Data > Cabang
Breadcrumbs::for('logistic.master.branch', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Cabang', route('logistic.master.branch'));
});

// Master Data > Pelanggan
Breadcrumbs::for('logistic.master.customer', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Pelanggan', route('logistic.master.customer'));
});

// Master Data > Supplier
Breadcrumbs::for('logistic.master.supplier', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Supplier', route('logistic.master.supplier'));
});

// Master Data > Gudang
Breadcrumbs::for('logistic.master.warehouse', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Gudang', route('logistic.master.warehouse'));
});

// Master Data > Kategori Produk
Breadcrumbs::for('logistic.master.category', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Kategori Produk', route('logistic.master.category'));
});

// Master Data > Satuan Produk
Breadcrumbs::for('logistic.master.unit', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Satuan Produk', route('logistic.master.unit'));
});

// Master Data > Produk
Breadcrumbs::for('logistic.master.product', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Data Produk', route('logistic.master.product'));
});

// Master Data Finansial > Mata Uang
Breadcrumbs::for('business.finance.currency', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Mata Uang', route('business.finance.currency'));
});

// Master Data Finansial > Pajak
Breadcrumbs::for('business.finance.tax', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Pajak', route('business.finance.tax'));
});

// Master Data Finansial > COA
Breadcrumbs::for('business.finance.coa', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Chart of Account', route('business.finance.coa'));
});

// Inventory > Stock Balance
Breadcrumbs::for('logistic.inventory.balance', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Saldo Stok', route('logistic.inventory.balance'));
});

// Inventory > Stock Card
Breadcrumbs::for('logistic.inventory.card', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Kartu Stok', route('logistic.inventory.card'));
});

// Inventory > Stock Adjustment
Breadcrumbs::for('logistic.inventory.adjustment', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Penyesuaian Stok', route('logistic.inventory.adjustment'));
});

// Inventory > Stock Transfer
Breadcrumbs::for('logistic.inventory.transfer', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Transfer Stok', route('logistic.inventory.transfer'));
});

// Inventory > Stock Opname
Breadcrumbs::for('logistic.inventory.opname', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Stock Opname', route('logistic.inventory.opname'));
});

// Inventory > Stock Waste
Breadcrumbs::for('logistic.inventory.waste', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Inventory', '#');
    $trail->push('Waste (Pembuangan)', route('logistic.inventory.waste'));
});


// Purchasing > Purchase Request
Breadcrumbs::for('logistic.purchasing.request', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Purchasing', '#');
    $trail->push('Purchase Request', route('logistic.purchasing.request'));
});

// Purchasing > Purchase Order
Breadcrumbs::for('logistic.purchasing.order', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Purchasing', '#');
    $trail->push('Purchase Order', route('logistic.purchasing.order'));
});

// Purchasing > Goods Receipt
Breadcrumbs::for('logistic.purchasing.receipt', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Purchasing', '#');
    $trail->push('Goods Receipt', route('logistic.purchasing.receipt'));
});

// Purchasing > Supplier Invoice
Breadcrumbs::for('logistic.purchasing.invoice', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Purchasing', '#');
    $trail->push('Supplier Invoice', route('logistic.purchasing.invoice'));
});

// Purchasing > Supplier Payment
Breadcrumbs::for('logistic.purchasing.payment', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Purchasing', '#');
    $trail->push('Pembayaran Supplier', route('logistic.purchasing.payment'));
});

// Master Data > Resep (Recipe)
Breadcrumbs::for('logistic.master.recipe', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Resep Produk', route('logistic.master.recipe'));
});

// Master Data > Stasiun Produksi
Breadcrumbs::for('logistic.master.station', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Master Data', '#');
    $trail->push('Stasiun Produksi', route('logistic.master.station'));
});

// Operational > POS Terminal
Breadcrumbs::for('operational.pos.terminal', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('POS', '#');
    $trail->push('POS Terminal', route('operational.pos.terminal'));
});

// Operational > Shift Kasir
Breadcrumbs::for('operational.pos.shift', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('POS', '#');
    $trail->push('Buka/Tutup Shift', route('operational.pos.shift'));
});

// Restaurant > Kitchen Display
Breadcrumbs::for('operational.restaurant.kitchen', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Restaurant', '#');
    $trail->push('Kitchen Display', route('operational.restaurant.kitchen'));
});

// Restaurant > Barista Display
Breadcrumbs::for('operational.restaurant.barista', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Restaurant', '#');
    $trail->push('Barista Display', route('operational.restaurant.barista'));
});

// Operational > Riwayat Penjualan
Breadcrumbs::for('operational.pos.history', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('POS', '#');
    $trail->push('Riwayat Penjualan', route('operational.pos.history'));
});

// Operational > Refund Transaksi
Breadcrumbs::for('operational.pos.refund', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('POS', '#');
    $trail->push('Refund Transaksi', route('operational.pos.refund'));
});

// Finance > Jurnal Umum
Breadcrumbs::for('business.finance.journal', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Jurnal Umum', route('business.finance.journal'));
});

// Finance > Hutang (AP)
Breadcrumbs::for('business.finance.payable', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Hutang (AP)', route('business.finance.payable'));
});

// Finance > Piutang (AR)
Breadcrumbs::for('business.finance.receivable', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Piutang (AR)', route('business.finance.receivable'));
});

// Finance > Buku Besar
Breadcrumbs::for('business.finance.ledger', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Buku Besar', route('business.finance.ledger'));
});

// Finance > Laba Rugi
Breadcrumbs::for('business.finance.profit_loss', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Laba Rugi', route('business.finance.profit_loss'));
});

// Finance > Neraca
Breadcrumbs::for('business.finance.balance_sheet', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Neraca', route('business.finance.balance_sheet'));
});

// CRM > Pelanggan
Breadcrumbs::for('business.crm.customer', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('CRM', '#');
    $trail->push('Pelanggan', route('business.crm.customer'));
});

// CRM > Membership
Breadcrumbs::for('business.crm.membership.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('CRM');
    $trail->push('Membership', route('business.crm.membership.index'));
});

// Business > CRM > Loyalty
Breadcrumbs::for('business.crm.loyalty', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('CRM');
    $trail->push('Poin Loyalitas', route('business.crm.loyalty'));
});

// Business > CRM > Voucher
Breadcrumbs::for('business.crm.voucher.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('CRM');
    $trail->push('Voucher', route('business.crm.voucher.index'));
});

// Laporan > Sales
Breadcrumbs::for('business.reports.sales', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Laporan Penjualan', route('business.reports.sales'));
});

// Laporan > Penjualan per Produk
Breadcrumbs::for('business.reports.sales_itemized', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Penjualan per Produk', route('business.reports.sales_itemized'));
});

// Laporan > Stock
Breadcrumbs::for('business.reports.stock', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Laporan Stok', route('business.reports.stock'));
});

// Laporan > Food Cost
Breadcrumbs::for('business.reports.food_cost', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Laporan Food Cost', route('business.reports.food_cost'));
});

// Laporan > Pembelian
Breadcrumbs::for('business.reports.purchase', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Laporan Pembelian', route('business.reports.purchase'));
});

// Laporan > Pembelian per Produk
Breadcrumbs::for('business.reports.purchase_itemized', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Pembelian per Produk', route('business.reports.purchase_itemized'));
});

// Laporan > Dashboard Eksekutif
Breadcrumbs::for('business.reports.executive', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Dashboard Eksekutif', route('business.reports.executive'));
});

// Restaurant > Manajemen Meja
Breadcrumbs::for('operational.restaurant.tables', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Restaurant', '#');
    $trail->push('Manajemen Meja', route('operational.restaurant.tables'));
});

// Restaurant > Reservasi
Breadcrumbs::for('operational.restaurant.reservations', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Restaurant', '#');
    $trail->push('Reservasi', route('operational.restaurant.reservations'));
});

// Restaurant > Antrean Pesanan
Breadcrumbs::for('operational.restaurant.queue', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Restaurant', '#');
    $trail->push('Antrean Pesanan', route('operational.restaurant.queue'));
});

// Finance > Penerimaan Kas
Breadcrumbs::for('business.finance.cash_receipt.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Penerimaan Kas', route('business.finance.cash_receipt.index'));
});

// Finance > Pengeluaran Kas
Breadcrumbs::for('business.finance.cash_disbursement.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Keuangan', '#');
    $trail->push('Pengeluaran Kas', route('business.finance.cash_disbursement.index'));
});

// System > Finance Config
Breadcrumbs::for('system.settings.finance_config', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sistem & Pengaturan', '#');
    $trail->push('Konfigurasi Keuangan', route('system.settings.finance_config'));
});

