<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ApiController;

// Normalize request URI
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Router Sederhana
switch ($uri) {
    case '/':
        (new HomeController())->index();
        break;

    case '/menu':
        (new HomeController())->index();
        break;

    case '/pos':
        echo "<h1>Halaman POS Kasir</h1><p>Fondasi siap. Akan diimplementasikan pada Issue #4 & #5.</p><a href='/'>Kembali ke Beranda</a>";
        break;

    case '/admin':
        echo "<h1>Halaman Admin & Manajemen Produk</h1><p>Fondasi siap. Akan diimplementasikan pada Issue #3.</p><a href='/'>Kembali ke Beranda</a>";
        break;

    case '/api/health':
        (new ApiController())->health();
        break;

    case '/api/products':
        (new ApiController())->products();
        break;

    case '/api/categories':
        (new ApiController())->categories();
        break;

    default:
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>404 Not Found</h1><p>Halaman atau rute [{$uri}] tidak ditemukan.</p><a href='/'>Kembali ke Beranda</a>";
        break;
}
