<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $productModel = new Product();
        $products = $productModel->getActiveWithCategory();

        $this->render('home', [
            'title' => $settings['store_name'] ?? 'Pondok Makan Kang Naryo',
            'settings' => $settings,
            'products' => $products
        ]);
    }
}
