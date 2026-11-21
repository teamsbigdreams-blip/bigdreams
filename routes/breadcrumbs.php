<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home / Dashboard
Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// ==========================================
// KATEGORI: MASTER
// ==========================================

// Dummy/Virtual parent untuk Master (tidak memiliki URL khusus, atau bisa diarahkan ke salah satu index)
Breadcrumbs::for('master.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Master');
});

// Home > Master > Users
Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('master.index');
    $trail->push('Users', route('users.index'));
});

// ==========================================
// KATEGORI: SITES SETTINGS
// ==========================================

// Dummy/Virtual parent untuk Sites Settings
Breadcrumbs::for('settings.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Sites Settings');
});

// Home > Sites Settings > Master Menu
Breadcrumbs::for('master-menus.index', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.index');
    $trail->push('Master Menu', route('master-menus.index'));
});

// Home > Sites Settings > Role
Breadcrumbs::for('roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.index');
    $trail->push('Role', route('roles.index'));
});

// Home > Sites Settings > User Role
Breadcrumbs::for('user-roles.index', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.index');
    $trail->push('User Role', route('user-roles.index'));
});

// Home > Sites Settings > User Permission
Breadcrumbs::for('user-permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('settings.index');
    $trail->push('User Permission', route('user-permissions.index'));
});

// Home > Company (Index)
Breadcrumbs::for('companies.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Company', route('companies.index'));
});

// Home > Transaction Hourly (Index)
Breadcrumbs::for('transaction-hourlies.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Transaction Hourly', route('transaction-hourlies.index'));
});
