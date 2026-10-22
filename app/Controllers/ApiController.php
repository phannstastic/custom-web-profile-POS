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
            'app' => 'Custom Web Profile & POS Architecture',
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

    public function orderDetail(int $id): void
    {
        $orderModel = new \App\Models\Order();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            $this->json(['success' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $this->json([
            'success' => true,
            'order' => $order
        ]);
    }
}
