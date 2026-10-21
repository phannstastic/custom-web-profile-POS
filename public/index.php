<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ApiController;

use App\Controllers\AdminController;

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
        (new HomeController())->menu();
        break;

    case '/pos':
        (new \App\Controllers\PosController())->index();
        break;

    // --- Admin Routes ---
    case '/admin':
        (new AdminController())->dashboard();
        break;

    case '/admin/login':
        (new AdminController())->login();
        break;

    case '/admin/logout':
        (new AdminController())->logout();
        break;

    case '/admin/categories':
        (new AdminController())->categories();
        break;

    case '/admin/categories/create':
        (new AdminController())->createCategory();
        break;

    case '/admin/categories/update':
        (new AdminController())->updateCategory();
        break;

    case '/admin/categories/delete':
        (new AdminController())->deleteCategory();
        break;

    case '/admin/settings':
        (new AdminController())->settings();
        break;

    case '/admin/settings/update':
        (new AdminController())->updateSettings();
        break;

    case '/admin/products':
        (new AdminController())->products();
        break;

    case '/admin/products/create':
        (new AdminController())->createProduct();
        break;

    case '/admin/products/update':
        (new AdminController())->updateProduct();
        break;

    case '/admin/products/toggle-availability':
        (new AdminController())->toggleAvailability();
        break;

    case '/admin/products/toggle-featured':
        (new AdminController())->toggleFeatured();
        break;

    case '/admin/products/delete':
        (new AdminController())->deleteProduct();
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
