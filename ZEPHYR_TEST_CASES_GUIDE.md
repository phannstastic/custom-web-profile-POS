# Panduan Test Case Lengkap (Zephyr Scale / Squad & Jira)

Berikut adalah daftar **Test Case lengkap** yang terhubung langsung ke **4 Epic Jira (`KAN-4`, `KAN-5`, `KAN-6`, `KAN-7`)** yang baru saja Anda buat. File CSV siap-impor juga sudah dibuat di [`ZEPHYR_TEST_CASES.csv`](file:///c:/Users/ADVAN/Documents/VIBE/Pondok-Makan-Custom/ZEPHYR_TEST_CASES.csv).

---

## 📌 EPIC 1: `KAN-4` (Customer Website & Menu Gallery)

### 🔹 TC-PUB-001: Verifikasi Tampilan Beranda & Navigasi Publik
- **Tujuan**: Memastikan header, logo, jam operasional, dan peta lokasi Google Maps termuat tanpa error dan tanpa nama template lama.
- **Pre-Condition**: Web server lokal aktif (`http://127.0.0.1:8008`).
- **Priority**: High | **Tipe**: Functional & Visual
- **Steps**:
  1. Akses halaman beranda utama (`/`).
     - *Expected Result*: Halaman termuat dengan status HTTP 200, navbar bersih (hanya menampilkan Beranda & Lokasi).
  2. Periksa teks toko, badge tradisi kuliner, dan footer.
     - *Expected Result*: Nama toko dinamis sesuai database, tidak ada teks template Kang Naryo, footer tidak menampilkan link internal kasir/admin.

---

### 🔹 TC-PUB-002: Buka Modal Pop-Up Menu & Navigasi Slider Multi-Foto
- **Tujuan**: Memastikan pop-up detail menu terbuka dan foto bisa digeser (carousel slide) jika menu memiliki lebih dari 1 foto.
- **Pre-Condition**: Terdapat menu yang memiliki `gallery_images` > 1 foto.
- **Priority**: Critical | **Tipe**: E2E / UI Interaction
- **Steps**:
  1. Buka halaman buku menu digital (`/menu`).
     - *Expected Result*: Kartu menu termuat dengan foto cover dan badge ketersediaan.
  2. Klik pada salah satu kartu menu (misal: *Nasi Ayam Geprek*).
     - *Expected Result*: Modal pop-up terbuka di tengah layar, menampilkan foto menu besar, nomor foto (`1/3 Foto`), deskripsi lengkap, dan harga Rupiah.
  3. Klik tombol panah navigasi kanan (`>`) atau thumbnail kecil di bawahnya.
     - *Expected Result*: Gambar menu bergeser ke foto ke-2 dengan transisi halus, indikator dot slide berpindah.
  4. Klik tombol close (`X`) atau tekan tombol `Esc` keyboard.
     - *Expected Result*: Modal pop-up tertutup dan kembali ke daftar menu.

---

### 🔹 TC-PUB-003: Menu Single Foto Tidak Menampilkan Tombol Geser
- **Tujuan**: Memastikan produk yang hanya memiliki 1 foto tidak menampilkan tombol slider geser.
- **Pre-Condition**: Produk hanya memiliki 1 foto tunggal di database.
- **Priority**: Medium | **Tipe**: Negative / Boundary
- **Steps**:
  1. Klik kartu produk yang hanya memiliki 1 foto.
     - *Expected Result*: Modal detail menu terbuka menampilkan 1 foto diam.
  2. Periksa tombol navigasi panah kiri/kanan dan indikator dots.
     - *Expected Result*: Tombol panah `<` dan `>`, indikator titik dot, serta thumbnail kecil otomatis disembunyikan (*hidden*).

---

## 📌 EPIC 2: `KAN-5` (Point of Sale, Cart & Checkout)

### 🔹 TC-POS-001: Tambah Menu ke Keranjang & Kalkulasi Total Real-time
- **Tujuan**: Memastikan kasir dapat memilih menu dan total pesanan dihitung akurat secara real-time.
- **Pre-Condition**: Akun kasir aktif, berada di halaman `/pos`.
- **Priority**: Critical | **Tipe**: Functional & Business Logic
- **Steps**:
  1. Buka layar kasir POS (`/pos`).
     - *Expected Result*: Layar kasir termuat cepat, layout ringkas/compact tanpa gambar produk.
  2. Klik produk A (Rp 12.000) sebanyak 2 kali dan produk B (Rp 3.000) sebanyak 1 kali.
     - *Expected Result*: Keranjang kanan langsung terisi 2 item, total item = 3, subtotal = Rp 27.000.
  3. Tekan tombol minus (`-`) pada item produk A di keranjang.
     - *Expected Result*: Jumlah produk A berkurang menjadi 1, total keranjang otomatis terupdate menjadi Rp 15.000.

---

### 🔹 TC-POS-002: Checkout Pembayaran Tunai (Cash) & Auto Cetak Struk
- **Tujuan**: Memastikan proses pembayaran tunai mencatat order ke database dan menampilkan nota kasir.
- **Pre-Condition**: Minimal 1 item ada di keranjang POS.
- **Priority**: Critical | **Tipe**: End-to-End (E2E)
- **Steps**:
  1. Di layar POS, klik tombol **Bayar Tunai**.
     - *Expected Result*: Modal dialog pembayaran cash terbuka menampilkan ringkasan tagihan.
  2. Masukkan uang tunai dari pelanggan (misal: tagihan Rp 27.000, uang diterima Rp 50.000).
     - *Expected Result*: Sistem otomatis menghitung uang kembalian Rp 23.000 secara instan.
  3. Klik tombol **Konfirmasi Pembayaran**.
     - *Expected Result*: Transaksi tersimpan ke SQLite (`orders` & `order_items`), keranjang di-reset kosong, dan jendela cetak struk kasir termal 58mm/80mm otomatis muncul.

---

## 📌 EPIC 3: `KAN-7` (Payment Integration)

### 🔹 TC-PAY-001: Inisiasi Pembayaran QRIS Midtrans & Snap Popup
- **Tujuan**: Memastikan tombol pembayaran QRIS memicu pemanggilan Midtrans Snap dan memunculkan barcode QRIS.
- **Pre-Condition**: API Key Midtrans terisi di pengaturan admin.
- **Priority**: High | **Tipe**: Integration Test
- **Steps**:
  1. Di layar POS yang berisi order, klik tombol **Pembayaran QRIS**.
     - *Expected Result*: Tombol berubah menjadi state loading, request dikirim ke backend `/api/payment/create-qris`.
  2. Tunggu respon dari server Midtrans.
     - *Expected Result*: Modal Snap Midtrans terbuka di layar kasir, menampilkan QRIS dinamis dan hitung mundur kedaluwarsa.

---

### 🔹 TC-PAY-002: Batalkan Pembayaran QRIS Tanpa Macet (No Button Freeze)
- **Tujuan**: Memastikan kasir dapat menutup pembayaran QRIS kapan saja dan tombol kasir tidak macet/berputar terus.
- **Pre-Condition**: Modal Snap QRIS sedang terbuka.
- **Priority**: High | **Tipe**: UI Robustness & Exception Handling
- **Steps**:
  1. Klik tombol silang (`X`) pada popup Snap QRIS Midtrans untuk membatalkan pembayaran digital.
     - *Expected Result*: Modal QRIS tertutup dengan aman.
  2. Periksa kondisi tombol pembayaran QRIS dan tombol Bayar Tunai.
     - *Expected Result*: Kedua tombol kembali ke status normal (spinner loading hilang), kasir dapat langsung memilih metode bayar lain tanpa harus me-refresh halaman.

---

## 📌 EPIC 4: `KAN-6` (Admin Backoffice & Inventory)

### 🔹 TC-ADM-001: Autentikasi Login Backoffice Admin Sukses & Gagal
- **Tujuan**: Memvalidasi keamanan hak akses backoffice admin.
- **Pre-Condition**: Akun `admin` terdaftar di database.
- **Priority**: Critical | **Tipe**: Security & Auth
- **Steps**:
  1. Akses `/admin/login`, masukkan username `admin` dan password salah `wrongpass99`.
     - *Expected Result*: Login ditolak, muncul notifikasi merah *"Username atau Password salah"*.
  2. Masukkan password yang benar `admin123` lalu submit.
     - *Expected Result*: Login berhasil, redirect ke `/admin`, sidebar kiri menampilkan nama profil *"Administrator"*.

---

### 🔹 TC-ADM-002: Upload Banyak Foto Sekaligus (Galeri Slide) pada Menu
- **Tujuan**: Memastikan admin bisa mengunggah lebih dari 1 file foto menu sekaligus dan otomatis terkompresi WebP.
- **Pre-Condition**: Admin telah login, berada di `/admin/products`.
- **Priority**: High | **Tipe**: Functional & Media Processing
- **Steps**:
  1. Klik tombol **+ Tambah Menu Baru**.
     - *Expected Result*: Modal form tambah menu terbuka dengan field Nama, Kategori, Harga, Foto Cover, dan input *Upload Banyak Foto Sekaligus (Galeri Slide)*.
  2. Isi form dan pilih 3 file foto sekaligus pada input galeri (`gallery_images[]`).
  3. Klik tombol **Simpan Menu**.
     - *Expected Result*: Menu tersimpan, file foto otomatis terkompresi ke format `.webp`, kolom `gallery_images` terisi array JSON nama file, dan menu langsung memiliki fitur multi-slide di buku menu digital.

---

### 🔹 TC-ADM-003: Personalisasi Narasi Cerita & Teks Identitas Web Profil
- **Tujuan**: Memastikan admin dapat mengubah narasi sambutan, judul headline, dan cerita rasa restoran.
- **Pre-Condition**: Buka menu `/admin/settings`.
- **Priority**: High | **Tipe**: Customization & Content Management
- **Steps**:
  1. Buka menu **Identitas Toko & Tema** (`/admin/settings`).
  2. Isi kolom *Nama Toko*, *Tagline*, *Badge Sambutan Depan*, dan *Narasi Cerita Usaha & Cita Rasa*.
  3. Klik **Simpan Pengaturan**.
  4. Buka halaman depan web profil (`/`).
     - *Expected Result*: Seluruh teks di hero section depan langsung berubah sesuai narasi kustom yang baru saja disimpan.
