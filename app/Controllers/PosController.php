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

    public function submitCashOrder(): void
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload || empty($payload['items'])) {
            $this->json(['success' => false, 'error' => 'Data pesanan kosong'], 400);
        }

        $orderModel = new \App\Models\Order();
        $result = $orderModel->createCashOrder($payload, $payload['items']);

        if ($result['success']) {
            $this->json([
                'success' => true,
                'data' => $result,
                'receipt_url' => '/pos/receipt/' . $result['order_id'] . '?autoprint=1'
            ]);
        } else {
            $this->json(['success' => false, 'error' => $result['error'] ?? 'Gagal memproses pesanan'], 500);
        }
    }

    public function receipt(int $orderId): void
    {
        $orderModel = new \App\Models\Order();
        $order = $orderModel->getOrderWithItems($orderId);

        if (!$order) {
            http_response_code(404);
            echo "Struk transaksi tidak ditemukan.";
            exit;
        }

        $db = Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->render('pos/receipt', [
            'order' => $order,
            'settings' => $settings,
            'cashierName' => $_SESSION['user_name'] ?? 'Kasir Utama'
        ]);
    }
}
