# Pondok Makan Kang Naryo - Custom Web Profile & POS

Aplikasi Web Profil Interaktif & Sistem Kasir POS (Point of Sales) ultra-ringan menggunakan **PHP 8.2+ Native**, **SQLite PDO**, dan **Vanilla CSS / Alpine.js**.

---

## 🚀 Fitur Utama
1. **Core Ultra-Ringan**: Tanpa framework berat, arsitektur MVC sederhana berbasis autoloader PSR-4.
2. **SQLite Database**: Self-contained, portable, zero-configuration database dengan foreign key cascade.
3. **Web Profile & Digital Menu**: Profil rumah makan & katalog menu interaktif.
4. **POS Kasir**: Interface kasir cepat, kalkulasi keranjang, dan cetak struk thermal 58mm.
5. **Multi-Payment**: Mendukung Tunai (Cash) dan QRIS otomatis (Midtrans Snap API).

---

## 🛠️ Instalasi & Menjalankan Aplikasi

### 1. Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo_sqlite` aktif)
- Composer

### 2. Generate Autoloader
```bash
composer dump-autoload
```

### 3. Migrasi & Seeding Database
Jalankan script migrasi untuk membuat file database SQLite beserta tabel dan data default toko:
```bash
php database/migrate.php
```

### 4. Menjalankan Server Lokal
```bash
php -S 127.0.0.1:8000 -t public
```
Akses di browser: `http://localhost:8000`

---

## 📁 Struktur Direktori
```text
├── app/
│   ├── Controllers/   # Controller logika rute (Home, API, POS, Admin)
│   ├── Database/      # Singleton PDO SQLite Connection
│   └── Models/        # Model entitas (Product, Category, Order, User, dll.)
├── database/
│   └── migrate.php    # Script skema DDL & seeder data default
├── public/
│   └── index.php      # Front controller & custom router
├── views/             # File template antarmuka
├── composer.json      # PSR-4 Autoloading App\
└── .gitignore
```

---

## 🔐 Akun Default (Seeder)
- **Admin**: `username: admin` | `password: admin123`
- **Kasir**: `username: kasir` | `password: kasir123`
