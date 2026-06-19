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
Breadcrumbs::for('system.settings.users', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sistem', '#');
    $trail->push('Manajemen User', route('system.settings.users'));
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
    $trail->push('Penerimaan Barang', route('logistic.purchasing.receipt'));
});

