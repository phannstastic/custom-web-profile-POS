<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(): void
    {
        $settings = $this->getSettings();
        $productModel = new Product();
        $products = $productModel->getAllWithCategory();

        $this->render('public/landing', [
            'title' => ($settings['store_name'] ?? 'Restoran') . (!empty($settings['store_tagline']) ? ' - ' . $settings['store_tagline'] : ''),
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
            'title' => 'Menu - ' . ($settings['store_name'] ?? 'Restoran'),
            'settings' => $settings,
            'products' => $products,
            'categories' => $categories,
            'activeNav' => 'menu'
        ], 'layouts/main');
    }
}
