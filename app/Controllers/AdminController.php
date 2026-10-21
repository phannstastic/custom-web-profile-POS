<?php

namespace App\Controllers;

use App\Database\Auth;
use App\Database\Uploader;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::startSession();
    }

    public function login(): void
    {
        if (Auth::check()) {
            header('Location: /admin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (Auth::attempt($username, $password)) {
                $_SESSION['flash_success'] = 'Selamat datang kembali di Backoffice!';
                header('Location: /admin');
                exit;
            } else {
                $this->render('admin/login', [
                    'error' => 'Username atau Password / PIN salah!'
                ]);
            }
        }

        $this->render('admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /admin/login');
        exit;
    }

    public function dashboard(): void
    {
        Auth::requireAuth();

        $productModel = new Product();
        $categoryModel = new Category();

        $products = $productModel->getAllWithCategory();
        $categories = $categoryModel->allOrdered();

        $orderModel = new Order();
        $todaySummary = $orderModel->getSalesSummary(date('Y-m-d'), date('Y-m-d'));
        $recentOrders = array_slice($orderModel->getFilteredOrders(null, null, null, null, null), 0, 5);

        $this->render('admin/dashboard', [
            'title' => 'Dashboard Admin - Pondok Makan Kang Naryo',
            'currentUser' => Auth::user(),
            'products' => $products,
            'categories' => $categories,
            'todaySummary' => $todaySummary,
            'recentOrders' => $recentOrders,
            'adminActiveNav' => 'dashboard'
        ], 'layouts/admin');
    }

    public function settings(): void
    {
        Auth::requireAuth();

        $db = \App\Database\Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $this->render('admin/settings', [
            'title' => 'Identitas Usaha & Kustomisasi Tema - Admin',
            'currentUser' => Auth::user(),
            'settings' => $settings,
            'adminActiveNav' => 'settings'
        ], 'layouts/admin');
    }

    public function updateSettings(): void
    {
        Auth::requireAuth();

        $db = \App\Database\Connection::get();

        $keys = [
            'store_name',
            'store_tagline',
            'store_phone',
            'store_hours',
            'store_address',
            'receipt_footer',
            'theme_primary_color',
            'midtrans_server_key',
            'midtrans_client_key',
            'midtrans_is_production'
        ];

        $stmt = $db->prepare("INSERT INTO settings (key, value) VALUES (:key, :value) ON CONFLICT(key) DO UPDATE SET value = :value, updated_at = CURRENT_TIMESTAMP");

        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                $val = trim($_POST[$k]);
                $stmt->execute([':key' => $k, ':value' => $val]);

                // Sync store_phone juga ke store_whatsapp
                if ($k === 'store_phone') {
                    $stmt->execute([':key' => 'store_whatsapp', ':value' => $val]);
                }
            }
        }

        // Handle store_maps_embed / store_maps_url
        if (isset($_POST['store_maps_embed'])) {
            $mapsVal = trim($_POST['store_maps_embed']);
            $stmt->execute([':key' => 'store_maps_embed', ':value' => $mapsVal]);
            $stmt->execute([':key' => 'store_maps_url', ':value' => $mapsVal]);
        }

        // Handle Upload Logo Toko
        if (!empty($_FILES['store_logo']['name'])) {
            $logoName = Uploader::uploadAsset($_FILES['store_logo'], 'logo_');
            if ($logoName) {
                $stmt->execute([':key' => 'store_logo', ':value' => $logoName]);
            }
        }

        // Handle Upload Banner Toko
        if (!empty($_FILES['store_banner']['name'])) {
            $bannerName = Uploader::uploadAsset($_FILES['store_banner'], 'banner_');
            if ($bannerName) {
                $stmt->execute([':key' => 'store_banner', ':value' => $bannerName]);
            }
        }

        $_SESSION['flash_success'] = 'Pengaturan identitas toko & tema warna berhasil disimpan!';
        header('Location: /admin/settings');
        exit;
    }

    public function categories(): void
    {
        Auth::requireAuth();

        $categoryModel = new Category();
        $categories = $categoryModel->allOrdered();

        $this->render('admin/categories', [
            'title' => 'Kelola Kategori - Pondok Makan Kang Naryo',
            'currentUser' => Auth::user(),
            'categories' => $categories,
            'adminActiveNav' => 'categories'
        ], 'layouts/admin');
    }

    public function createCategory(): void
    {
        Auth::requireAuth();

        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '');

        if (!empty($name)) {
            $categoryModel = new Category();
            $categoryModel->create($name, $icon);
            $_SESSION['flash_success'] = "Kategori '{$name}' berhasil ditambahkan!";
        } else {
            $_SESSION['flash_error'] = "Nama kategori tidak boleh kosong!";
        }

        header('Location: /admin/categories');
        exit;
    }

    public function updateCategory(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? '');

        if ($id > 0 && !empty($name)) {
            $categoryModel = new Category();
            $categoryModel->update($id, $name, $icon);
            $_SESSION['flash_success'] = "Kategori berhasil diperbarui!";
        }

        header('Location: /admin/categories');
        exit;
    }

    public function deleteCategory(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $categoryModel = new Category();
            $categoryModel->delete($id);
            $_SESSION['flash_success'] = "Kategori berhasil dihapus!";
        }

        header('Location: /admin/categories');
        exit;
    }

    public function products(): void
    {
        Auth::requireAuth();

        $productModel = new Product();
        $categoryModel = new Category();

        $products = $productModel->getAllWithCategory();
        $categories = $categoryModel->allOrdered();

        $this->render('admin/products', [
            'title' => 'Kelola Menu Produk - Pondok Makan Kang Naryo',
            'currentUser' => Auth::user(),
            'products' => $products,
            'categories' => $categories,
            'adminActiveNav' => 'products'
        ], 'layouts/admin');
    }

    public function createProduct(): void
    {
        Auth::requireAuth();

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $price = (int) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if (empty($name)) {
            $_SESSION['flash_error'] = 'Nama menu wajib diisi!';
            header('Location: /admin/products');
            exit;
        }

        $imageFilename = null;
        if (!empty($_FILES['image']['name'])) {
            $imageFilename = Uploader::uploadProductImage($_FILES['image']);
        }

        $productModel = new Product();
        $productModel->create([
            'name'         => $name,
            'category_id'  => $categoryId,
            'price'        => $price,
            'description'  => $description,
            'image'        => $imageFilename,
            'is_available' => $isAvailable,
            'is_featured'  => $isFeatured,
        ]);

        $_SESSION['flash_success'] = "Menu '{$name}' berhasil ditambahkan!";
        header('Location: /admin/products');
        exit;
    }

    public function updateProduct(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $price = (int) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

        if ($id > 0 && !empty($name)) {
            $data = [
                'name'         => $name,
                'category_id'  => $categoryId,
                'price'        => $price,
                'description'  => $description,
                'is_available' => $isAvailable,
                'is_featured'  => $isFeatured,
            ];

            if (!empty($_FILES['image']['name'])) {
                $imageFilename = Uploader::uploadProductImage($_FILES['image']);
                if ($imageFilename) {
                    $data['image'] = $imageFilename;
                }
            }

            $productModel = new Product();
            $productModel->updateProduct($id, $data);
            $_SESSION['flash_success'] = "Menu '{$name}' berhasil diperbarui!";
        }

        header('Location: /admin/products');
        exit;
    }

    public function toggleAvailability(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $productModel = new Product();
            $productModel->toggleAvailability($id);
            $_SESSION['flash_success'] = "Status ketersediaan stok menu berhasil diperbarui!";
        }

        header('Location: /admin/products');
        exit;
    }

    public function toggleFeatured(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $productModel = new Product();
            $productModel->toggleFeatured($id);
            $_SESSION['flash_success'] = "Status favorit menu berhasil diubah!";
        }

        header('Location: /admin/products');
        exit;
    }

    public function deleteProduct(): void
    {
        Auth::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $productModel = new Product();
            $productModel->delete($id);
            $_SESSION['flash_success'] = "Menu berhasil dihapus dari sistem!";
        }

        header('Location: /admin/products');
        exit;
    }

    public function reports(): void
    {
        Auth::requireAuth();

        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $paymentMethod = $_GET['payment_method'] ?? 'all';
        $paymentStatus = $_GET['payment_status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $orderModel = new Order();
        $summary = $orderModel->getSalesSummary($startDate, $endDate, $paymentMethod, $paymentStatus);
        $orders = $orderModel->getFilteredOrders($startDate, $endDate, $paymentMethod, $paymentStatus, $search);

        $this->render('admin/reports', [
            'title' => 'Laporan Ringkasan Penjualan & Riwayat Transaksi - Admin',
            'currentUser' => Auth::user(),
            'adminActiveNav' => 'reports',
            'summary' => $summary,
            'orders' => $orders,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'search' => $search
            ]
        ], 'layouts/admin');
    }

    public function exportCsv(): void
    {
        Auth::requireAuth();

        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $paymentMethod = $_GET['payment_method'] ?? 'all';
        $paymentStatus = $_GET['payment_status'] ?? 'all';
        $search = trim($_GET['search'] ?? '');

        $orderModel = new Order();
        $orders = $orderModel->getFilteredOrders($startDate, $endDate, $paymentMethod, $paymentStatus, $search);

        $filename = 'laporan-penjualan-' . $startDate . '-sd-' . $endDate . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM untuk kompatibilitas Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header CSV
        fputcsv($output, [
            'No',
            'No. Nota / Invoice',
            'Tanggal & Waktu',
            'Nama Pelanggan',
            'No. Meja',
            'Metode Pembayaran',
            'Status Transaksi',
            'Daftar Item Menu',
            'Total Belanja (Rp)',
            'Uang Diterima (Rp)',
            'Kembalian (Rp)',
            'Catatan Pesanan'
        ]);

        $no = 1;
        $totalRevenue = 0;

        foreach ($orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $totalRevenue += (int) $order['total_amount'];
            }

            fputcsv($output, [
                $no++,
                $order['invoice_number'],
                $order['created_at'],
                $order['customer_name'] ?? 'Pelanggan',
                $order['table_number'] ?? '-',
                strtoupper($order['payment_method']),
                strtoupper($order['payment_status']),
                $order['items_summary'] ?? '-',
                (int) $order['total_amount'],
                (int) $order['cash_paid'],
                (int) $order['cash_change'],
                $order['notes'] ?? '-'
            ]);
        }

        // Baris ringkasan
        fputcsv($output, []);
        fputcsv($output, ['', '', '', '', '', '', 'TOTAL OMSET (LUNAS)', '', $totalRevenue, '', '', '']);

        fclose($output);
        exit;
    }
}
