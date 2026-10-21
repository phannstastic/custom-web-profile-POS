<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    private function getSettings(): array
    {
        $db = Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    public function index(): void
    {
        $settings = $this->getSettings();
        $productModel = new Product();
        $products = $productModel->getAllWithCategory();

        $this->render('public/landing', [
            'title' => ($settings['store_name'] ?? 'Pondok Makan Kang Naryo') . ' - Masakan Khas Tradisional & Ikan Bakar',
            'settings' => $settings,
            'products' => $products,
            'activeNav' => 'home'
        ], 'layouts/main');
    }

    public function menu(): void
    {
        $settings = $this->getSettings();
        $productModel = new Product();
        $categoryModel = new Category();

        $products = $productModel->getAllWithCategory();
        $categories = $categoryModel->allOrdered();

        $this->render('public/digital-menu', [
            'title' => 'Buku Menu Digital - ' . ($settings['store_name'] ?? 'Pondok Makan Kang Naryo'),
            'settings' => $settings,
            'products' => $products,
            'categories' => $categories,
            'activeNav' => 'menu'
        ], 'layouts/main');
    }
}
