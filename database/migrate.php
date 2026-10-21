<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Connection;

echo "Memulai migrasi database SQLite...\n";

$db = Connection::get();

// 1. Tabel settings
$db->exec("
    CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT NOT NULL UNIQUE,
        value TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// 2. Tabel users
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'cashier', -- 'admin', 'cashier'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// 3. Tabel categories
$db->exec("
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        icon TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// 4. Tabel products
$db->exec("
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT,
        price INTEGER NOT NULL DEFAULT 0,
        image TEXT,
        is_available INTEGER NOT NULL DEFAULT 1,
        is_featured INTEGER NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    );
");

// 5. Tabel orders
$db->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        invoice_number TEXT NOT NULL UNIQUE,
        customer_name TEXT,
        table_number TEXT,
        total_amount INTEGER NOT NULL DEFAULT 0,
        payment_method TEXT NOT NULL DEFAULT 'cash', -- 'cash', 'qris'
        payment_status TEXT NOT NULL DEFAULT 'pending', -- 'pending', 'paid', 'cancelled'
        cash_paid INTEGER DEFAULT 0,
        cash_change INTEGER DEFAULT 0,
        snap_token TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// 6. Tabel order_items
$db->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        price INTEGER NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        subtotal INTEGER NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    );
");

echo "Tabel berhasil disiapkan.\n";
echo "Menjalankan seeding data default...\n";

// --- SEEDER ---

// Seeder Settings
$settings = [
    'store_name' => 'Pondok Makan Kang Naryo',
    'store_tagline' => 'Cita Rasa Masakan Khas Tradisional & Ikan Bakar Sedap',
    'store_address' => 'Jl. Kuliner Lezat No. 88, Malioboro, Yogyakarta',
    'store_phone' => '081234567890',
    'store_whatsapp' => '6281234567890',
    'store_hours' => 'Setiap Hari: 10.00 - 22.00 WIB',
    'store_maps_url' => 'https://maps.google.com/maps?q=Yogyakarta&t=&z=14&ie=UTF8&iwloc=&output=embed',
    'receipt_footer' => 'Terima Kasih Atas Kunjungan Anda! Selamat Menikmati.'
];

$stmtSetting = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (:key, :value)");
foreach ($settings as $k => $v) {
    $stmtSetting->execute([':key' => $k, ':value' => $v]);
}

// Seeder Users (Admin & Kasir)
$users = [
    [
        'name' => 'Administrator Kang Naryo',
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_BCRYPT),
        'role' => 'admin'
    ],
    [
        'name' => 'Kasir 1',
        'username' => 'kasir',
        'password' => password_hash('kasir123', PASSWORD_BCRYPT),
        'role' => 'cashier'
    ]
];

$stmtUser = $db->prepare("INSERT OR IGNORE INTO users (name, username, password, role) VALUES (:name, :username, :password, :role)");
foreach ($users as $user) {
    $stmtUser->execute([
        ':name' => $user['name'],
        ':username' => $user['username'],
        ':password' => $user['password'],
        ':role' => $user['role'],
    ]);
}

// Seeder Kategori
$categories = [
    ['name' => 'Makanan Utama', 'slug' => 'makanan-utama', 'icon' => 'fa-utensils'],
    ['name' => 'Minuman Segar', 'slug' => 'minuman-segar', 'icon' => 'fa-glass-water'],
    ['name' => 'Camilan & Tambahan', 'slug' => 'camilan-tambahan', 'icon' => 'fa-bowl-food'],
];

$stmtCategory = $db->prepare("INSERT OR IGNORE INTO categories (name, slug, icon) VALUES (:name, :slug, :icon)");
foreach ($categories as $cat) {
    $stmtCategory->execute([
        ':name' => $cat['name'],
        ':slug' => $cat['slug'],
        ':icon' => $cat['icon'],
    ]);
}

// Map category IDs
$catMap = [];
$catQuery = $db->query("SELECT id, slug FROM categories")->fetchAll();
foreach ($catQuery as $row) {
    $catMap[$row['slug']] = $row['id'];
}

// Seeder Produk Menu
$products = [
    [
        'category_slug' => 'makanan-utama',
        'name' => 'Ayam Bakar Madu Spesial',
        'slug' => 'ayam-bakar-madu-spesial',
        'description' => 'Ayam bakar dengan bumbu kecap madu khas rempah Pondok Makan Kang Naryo, gurih manis meresap.',
        'price' => 22000,
        'image' => 'ayam-bakar.jpg',
        'is_available' => 1,
        'is_featured' => 1
    ],
    [
        'category_slug' => 'makanan-utama',
        'name' => 'Gurame Bakar Sambal Terasi',
        'slug' => 'gurame-bakar-sambal-terasi',
        'description' => 'Ikan gurame segar dibakar dengan arang batok kelapa disajikan lengkap bersama lalapan & sambal terasi pedas nampol.',
        'price' => 38000,
        'image' => 'gurame-bakar.jpg',
        'is_available' => 1,
        'is_featured' => 1
    ],
    [
        'category_slug' => 'makanan-utama',
        'name' => 'Bebek Goreng Krispi Sambal Korek',
        'slug' => 'bebek-goreng-krispi',
        'description' => 'Bebek empuk tidak amis dengan kulit krispi renyah dan sambal korek bawang panas.',
        'price' => 28000,
        'image' => 'bebek-goreng.jpg',
        'is_available' => 1,
        'is_featured' => 1
    ],
    [
        'category_slug' => 'minuman-segar',
        'name' => 'Es Teh Manis Jumbo',
        'slug' => 'es-teh-manis-jumbo',
        'description' => 'Teh melati seduh tradisional yang harum dan segar dengan gula tebu asli porsi jumbo.',
        'price' => 5000,
        'image' => 'es-teh.jpg',
        'is_available' => 1,
        'is_featured' => 1
    ],
    [
        'category_slug' => 'minuman-segar',
        'name' => 'Es Jeruk Peras Murni',
        'slug' => 'es-jeruk-peras',
        'description' => 'Jeruk peras asli manis alami segar kaya vitamin C.',
        'price' => 7000,
        'image' => 'es-jeruk.jpg',
        'is_available' => 1,
        'is_featured' => 0
    ],
    [
        'category_slug' => 'camilan-tambahan',
        'name' => 'Tahu & Tempe Mendoan Hangat',
        'slug' => 'mendoan-hangat',
        'description' => 'Tempe dan tahu mendoan bumbu ketumbar daun bawang disajikan dengan kecap cabe rawit.',
        'price' => 10000,
        'image' => 'mendoan.jpg',
        'is_available' => 1,
        'is_featured' => 0
    ],
    [
        'category_slug' => 'makanan-utama',
        'name' => 'Sop Iga Sapi Rempah Gurih',
        'slug' => 'sop-iga-sapi-rempah',
        'description' => 'Iga sapi pilihan dimasak empuk dengan kuah kaldu rempah bening gurih, wortel, dan kentang segar.',
        'price' => 45000,
        'image' => 'sop-iga.jpg',
        'is_available' => 0, // HABIS (Demonstrasi kriteria selesai issue 2)
        'is_featured' => 0
    ],
    [
        'category_slug' => 'minuman-segar',
        'name' => 'Jus Alpukat Kerok Kental',
        'slug' => 'jus-alpukat-kerok',
        'description' => 'Alpukat mentega asli disajikan kental dengan susu cokelat manis legit.',
        'price' => 14000,
        'image' => 'jus-alpukat.jpg',
        'is_available' => 0, // HABIS (Demonstrasi kriteria selesai issue 2)
        'is_featured' => 1
    ]
];

$stmtProduct = $db->prepare("
    INSERT OR IGNORE INTO products (category_id, name, slug, description, price, image, is_available, is_featured)
    VALUES (:category_id, :name, :slug, :description, :price, :image, :is_available, :is_featured)
");

foreach ($products as $prod) {
    $catId = $catMap[$prod['category_slug']] ?? null;
    $stmtProduct->execute([
        ':category_id' => $catId,
        ':name' => $prod['name'],
        ':slug' => $prod['slug'],
        ':description' => $prod['description'],
        ':price' => $prod['price'],
        ':image' => $prod['image'],
        ':is_available' => $prod['is_available'],
        ':is_featured' => $prod['is_featured']
    ]);
}

echo "Migrasi dan Seeder berhasil dieksekusi!\n";
