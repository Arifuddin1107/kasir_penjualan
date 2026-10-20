<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root URL Redirect
$routes->get('/', function () {
    if (session()->get('logged_in')) {
        return session()->get('role') === 'kasir'
            ? redirect()->to('/pos')
            : redirect()->to('/dashboard');
    }
    return redirect()->to('/login');
});

// Autentikasi
$routes->get('/login', 'Auth::login');
$routes->post('/login/attempt', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->post('/logout', 'Auth::logout');

// Rute Kasir & Transaksi (Bisa diakses oleh Kasir dan Admin)
$routes->group('', ['filter' => 'role:admin,kasir'], static function ($routes) {
    $routes->get('/pos', 'Pos::index');
    $routes->get('/pos/search', 'Pos::search');
    $routes->post('/pos/checkout', 'Pos::checkout');

    $routes->get('/transaksi/struk/(:num)', 'Transaksi::struk/$1');
    $routes->get('/transaksi/detail/(:num)', 'Transaksi::detailJson/$1');
});

// Rute Finansial & Master Data Admin (Secara Mutlak Memblokir Kasir)
$routes->group('', ['filter' => 'role:admin'], static function ($routes) {
    // Dashboard Finansial & Analitik
    $routes->get('/dashboard', 'Dashboard::index');

    // Master Produk & Stok Menipis
    $routes->get('/produk', 'Produk::index');
    $routes->post('/produk/store', 'Produk::store');
    $routes->post('/produk/update/(:num)', 'Produk::update/$1');
    $routes->get('/produk/delete/(:num)', 'Produk::delete/$1');

    // Master Kategori
    $routes->get('/kategori', 'Kategori::index');
    $routes->post('/kategori/store', 'Kategori::store');
    $routes->post('/kategori/update/(:num)', 'Kategori::update/$1');
    $routes->get('/kategori/delete/(:num)', 'Kategori::delete/$1');

    // Mutasi Stok
    $routes->get('/mutasi', 'MutasiStok::index');
    $routes->post('/mutasi/adjust', 'MutasiStok::adjust');

    // Laporan & Ekspor Excel
    $routes->get('/laporan', 'Laporan::index');
    $routes->get('/laporan/export', 'Laporan::exportExcel');

    // Manajemen Pengguna
    $routes->get('/pengguna', 'Pengguna::index');
    $routes->post('/pengguna/store', 'Pengguna::store');
    $routes->post('/pengguna/update/(:num)', 'Pengguna::update/$1');
    $routes->post('/pengguna/unlock/(:num)', 'Pengguna::unlock/$1');
    $routes->get('/pengguna/delete/(:num)', 'Pengguna::delete/$1');

    // Pengaturan Toko & Printer
    $routes->get('/pengaturan', 'Pengaturan::index');
    $routes->post('/pengaturan/update', 'Pengaturan::update');
});
