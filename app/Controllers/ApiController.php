<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Product;

class ApiController extends Controller
{
    public function health(): void
    {
        $this->json([
            'status' => 'ok',
            'app' => 'Pondok Makan Kang Naryo Core Architecture',
            'database' => 'SQLite Connected',
            'php_version' => PHP_VERSION,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function products(): void
    {
        $productModel = new Product();
        $this->json([
            'status' => 'success',
            'data' => $productModel->getActiveWithCategory()
        ]);
    }

    public function categories(): void
    {
        $db = Connection::get();
        $categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
        $this->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
