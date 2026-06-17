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
