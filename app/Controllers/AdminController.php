<?php

namespace App\Controllers;

use App\Database\Auth;
use App\Database\Uploader;
use App\Models\Product;
use App\Models\Category;

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

        $this->render('admin/dashboard', [
            'title' => 'Dashboard Admin - Pondok Makan Kang Naryo',
            'currentUser' => Auth::user(),
            'products' => $products,
            'categories' => $categories,
            'adminActiveNav' => 'dashboard'
        ], 'layouts/admin');
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
}
