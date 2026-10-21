<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Dashboard') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-stone-100 text-stone-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-stone-900 text-stone-300 shrink-0 md:min-h-screen flex flex-col justify-between border-r border-stone-800">
        <div>
            <!-- Admin Brand Header -->
            <div class="p-5 border-b border-stone-800 flex items-center justify-between">
                <a href="/admin" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-amber-500 text-white flex items-center justify-center font-bold shadow-md shadow-brand-500/30">
                        <i class="fa-solid fa-utensils text-sm"></i>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-white tracking-tight leading-tight">Admin Kang Naryo</span>
                        <span class="text-[10px] text-stone-400 font-medium">Backoffice Management</span>
                    </div>
                </a>
            </div>

            <!-- Menu List -->
            <nav class="p-3 space-y-1 text-sm font-semibold">
                <a href="/admin" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors <?= ($adminActiveNav ?? '') === 'dashboard' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'hover:bg-stone-800 text-stone-300' ?>">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span>Ringkasan</span>
                </a>

                <a href="/admin/products" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors <?= ($adminActiveNav ?? '') === 'products' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'hover:bg-stone-800 text-stone-300' ?>">
                    <i class="fa-solid fa-bowl-food w-5 text-center"></i>
                    <span>Kelola Menu & Produk</span>
                </a>

                <a href="/admin/categories" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors <?= ($adminActiveNav ?? '') === 'categories' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'hover:bg-stone-800 text-stone-300' ?>">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Kelola Kategori</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[10px] uppercase tracking-wider text-stone-500 font-bold">
                    Tautan Cepat
                </div>

                <a href="/" target="_blank" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 transition-colors text-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                    <span>Halaman Publik Web</span>
                </a>

                <a href="/menu" target="_blank" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 transition-colors text-xs">
                    <i class="fa-solid fa-book-open w-5 text-center"></i>
                    <span>Buku Menu Digital</span>
                </a>

                <a href="/pos" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 transition-colors text-xs">
                    <i class="fa-solid fa-cash-register w-5 text-center"></i>
                    <span>Buka POS Kasir</span>
                </a>
            </nav>
        </div>

        <!-- User Info & Logout -->
        <div class="p-4 border-t border-stone-800 bg-stone-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-stone-700 text-stone-300 flex items-center justify-center font-bold text-xs uppercase">
                        <?= substr($currentUser['name'] ?? 'A', 0, 1) ?>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></p>
                        <p class="text-[10px] text-stone-400 font-medium">@<?= htmlspecialchars($currentUser['username'] ?? 'admin') ?></p>
                    </div>
                </div>
                <a href="/admin/logout" title="Keluar Akun" class="p-2 text-stone-400 hover:text-red-400 hover:bg-stone-800 rounded-lg transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Content Workspace -->
    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">
        
        <!-- Topbar Notification / Alert -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="bg-emerald-600 text-white px-6 py-3 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-md">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                </span>
                <button onclick="this.parentElement.remove()" class="text-emerald-200 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="bg-red-600 text-white px-6 py-3 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-md">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </span>
                <button onclick="this.parentElement.remove()" class="text-red-200 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <main class="p-4 sm:p-8 flex-grow">
            <?= $content ?>
        </main>

        <footer class="p-4 sm:px-8 border-t border-stone-200 text-stone-400 text-xs flex items-center justify-between bg-white">
            <span>Pondok Makan Kang Naryo • Backoffice Admin</span>
            <span>Versi SQLite 1.0</span>
        </footer>
    </div>

</body>
</html>
