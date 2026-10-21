<?php

namespace App\Controllers;

use App\Database\Auth;
use App\Database\Connection;
use App\Models\Product;
use App\Models\Category;

class PosController extends Controller
{
    public function __construct()
    {
        Auth::startSession();
    }

    public function index(): void
    {
        // Akses Kasir bisa login atau langsung buka kasir
        $db = Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $productModel = new Product();
        $categoryModel = new Category();

        // Ambil produk yang tersedia (is_available = 1) untuk layar kasir
        $products = $productModel->getActiveWithCategory();
        $categories = $categoryModel->allOrdered();

        $this->render('pos/screen', [
            'title' => 'Layar Kasir POS - ' . ($settings['store_name'] ?? 'Pondok Makan Kang Naryo'),
            'settings' => $settings,
            'products' => $products,
            'categories' => $categories,
            'currentUser' => Auth::user()
        ]);
    }
}
