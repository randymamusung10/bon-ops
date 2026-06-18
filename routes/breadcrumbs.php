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
