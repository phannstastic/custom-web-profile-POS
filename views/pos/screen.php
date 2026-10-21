<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title ?? 'Layar Kasir POS') ?></title>
    
    <?php
        $primaryColor = !empty($settings['theme_primary_color']) ? htmlspecialchars($settings['theme_primary_color']) : '#ea580c';
        $storeLogo = !empty($settings['store_logo']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_logo']) 
                     ? '/assets/uploads/' . htmlspecialchars($settings['store_logo']) 
                     : null;
    ?>
    <?php if ($storeLogo): ?>
        <link rel="icon" type="image/png" href="<?= $storeLogo ?>">
        <link rel="apple-touch-icon" href="<?= $storeLogo ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="/assets/images/icon-192.png">
        <link rel="apple-touch-icon" href="/assets/images/icon-192.png">
    <?php endif; ?>

    <!-- PWA Web App Manifest & Mobile Metas -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="<?= $primaryColor ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="POS Kang Naryo">

    <!-- Dexie.js (IndexedDB wrapper for low-device offline & instant caching) -->
    <script src="https://cdn.jsdelivr.net/npm/dexie@3.2.4/dist/dexie.min.js"></script>

    <!-- Midtrans Snap JS (Sandbox / Production) -->
    <?php
        $isMidtransProd = ($settings['midtrans_is_production'] ?? '0') === '1';
        $midtransClientKey = htmlspecialchars($settings['midtrans_client_key'] ?? '');
        $snapJsUrl = $isMidtransProd 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    ?>
    <script src="<?= $snapJsUrl ?>" data-client-key="<?= $midtransClientKey ?>"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: 'color-mix(in srgb, <?= $primaryColor ?> 10%, white)',
                            100: 'color-mix(in srgb, <?= $primaryColor ?> 20%, white)',
                            500: '<?= $primaryColor ?>',
                            600: '<?= $primaryColor ?>',
                            700: 'color-mix(in srgb, <?= $primaryColor ?> 85%, black)',
                            800: 'color-mix(in srgb, <?= $primaryColor ?> 70%, black)',
                            900: 'color-mix(in srgb, <?= $primaryColor ?> 55%, black)',
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; user-select: none; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-stone-100 text-stone-800 antialiased h-screen overflow-hidden flex flex-col"
      x-data="posApp()">

    <!-- Top POS Header -->
    <header class="bg-stone-900 text-white h-14 shrink-0 px-4 flex items-center justify-between border-b border-stone-800 z-30">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center gap-2 group" title="Kembali ke Beranda">
                <?php if ($storeLogo): ?>
                    <img src="<?= $storeLogo ?>" alt="Logo" class="w-8 h-8 rounded-lg object-contain">
                <?php else: ?>
                    <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white text-xs font-bold">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <span class="block text-sm font-bold tracking-tight text-white leading-tight">
                        <?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?>
                    </span>
                    <span class="text-[10px] text-amber-400 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Mode Kasir POS
                    </span>
                </div>
            </a>
        </div>

        <!-- Center Quick Search Bar -->
        <div class="hidden sm:block relative w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-stone-400 text-xs"></i>
            <input 
                type="text" 
                x-model="searchQuery" 
                placeholder="Cari menu (Ctrl+K)..." 
                class="w-full bg-stone-800 border border-stone-700 rounded-xl pl-8 pr-3 py-1.5 text-xs text-white placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-brand-500"
            >
            <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-white">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Header Actions (Full Screen, Install App, Admin Link, Cart badge for Mobile) -->
        <div class="flex items-center gap-2">
            <!-- PWA Install Prompt Button -->
            <button 
                x-show="deferredPrompt !== null" 
                @click="promptInstallApp()" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-amber-500 to-brand-600 hover:from-amber-600 hover:to-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-500/20 transition-all animate-pulse"
                title="Pasang Aplikasi Kasir ke Komputer / HP">
                <i class="fa-solid fa-download text-xs"></i>
                <span class="hidden md:inline">Pasang Aplikasi</span>
            </button>

            <!-- Fullscreen Toggle Button -->
            <button 
                @click="toggleFullScreen()" 
                class="hidden sm:inline-flex items-center justify-center w-8 h-8 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 transition-colors" 
                title="Layar Penuh (Standalone Mode)">
                <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'" class="text-xs"></i>
            </button>

            <!-- Mobile Toggle View: Menu vs Keranjang -->
            <button 
                @click="mobileTab = (mobileTab === 'menu' ? 'cart' : 'menu')" 
                class="lg:hidden relative px-3 py-1.5 rounded-xl bg-brand-600 text-white text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-cart-shopping"></i>
                <span x-text="mobileTab === 'menu' ? 'Lihat Pesanan' : 'Pilih Menu'"></span>
                <span class="px-1.5 py-0.2 rounded-full bg-white text-stone-900 text-[10px] font-extrabold" x-text="cartTotalCount"></span>
            </button>

            <a href="/admin" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-stone-300 rounded-xl text-xs font-semibold transition-colors">
                <i class="fa-solid fa-gear text-xs"></i>
                <span>Admin</span>
            </a>

            <div class="hidden md:flex items-center gap-2 pl-2 border-l border-stone-700 text-xs text-stone-400">
                <i class="fa-solid fa-user-circle text-base"></i>
                <span><?= htmlspecialchars($currentUser['name'] ?? 'Kasir') ?></span>
            </div>
        </div>
    </header>

    <!-- Main Workspace (2 Kolom: Kiri Menu, Kanan Keranjang) -->
    <div class="flex-grow flex overflow-hidden relative">

        <!-- KOLOM KIRI: GRID MENU & KATEGORI (Touch-Friendly) -->
        <section 
            :class="mobileTab === 'menu' ? 'flex' : 'hidden lg:flex'"
            class="flex-1 flex-col h-full bg-stone-100 overflow-hidden border-r border-stone-200">
            
            <!-- Mobile Search Bar (Only visible on small screens) -->
            <div class="p-3 bg-white border-b border-stone-200 sm:hidden">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-stone-400 text-xs"></i>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        placeholder="Cari menu makanan / minuman..." 
                        class="w-full bg-stone-50 border border-stone-300 rounded-xl pl-8 pr-3 py-1.5 text-xs text-stone-800 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    >
                </div>
            </div>

            <!-- Category Filter Ribbon -->
            <div class="p-3 bg-white border-b border-stone-200 shrink-0 flex items-center gap-2 overflow-x-auto no-scrollbar">
                <button 
                    @click="selectedCategory = 'all'" 
                    :class="selectedCategory === 'all' 
                        ? 'bg-brand-600 text-white shadow-sm' 
                        : 'bg-stone-100 text-stone-700 hover:bg-stone-200'"
                    class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-border-all text-xs"></i>
                    <span>Semua Menu</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedCategory === 'all' ? 'bg-white/20' : 'bg-stone-200'" x-text="products.length"></span>
                </button>

                <template x-for="cat in categories" :key="cat.id">
                    <button 
                        @click="selectedCategory = cat.slug" 
                        :class="selectedCategory === cat.slug 
                            ? 'bg-brand-600 text-white shadow-sm' 
                            : 'bg-stone-100 text-stone-700 hover:bg-stone-200'"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer">
                        <i :class="cat.icon ? 'fa-solid ' + cat.icon : 'fa-solid fa-utensils'" class="text-xs"></i>
                        <span x-text="cat.name"></span>
                    </button>
                </template>
            </div>

            <!-- Products Grid Container -->
            <div class="flex-grow p-4 overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3.5">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div 
                            @click="addToCart(product)"
                            class="group bg-white rounded-2xl border border-stone-200/90 shadow-xs hover:shadow-md hover:border-brand-500 active:scale-95 transition-all duration-150 p-3.5 flex flex-col justify-between cursor-pointer select-none relative overflow-hidden">
                            
                            <!-- Product Visual & Thumbnail -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 bg-stone-100 px-2 py-0.5 rounded-md line-clamp-1" x-text="product.category_name || 'Menu'"></span>
                                    <template x-if="getItemQtyInCart(product.id) > 0">
                                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-brand-600 text-white text-[11px] font-extrabold shadow-sm" x-text="getItemQtyInCart(product.id)"></span>
                                    </template>
                                </div>

                                <!-- Optional Image Thumbnail -->
                                <template x-if="product.image">
                                    <div class="w-full h-28 mb-2.5 rounded-xl overflow-hidden bg-stone-100 relative">
                                        <img :src="'/assets/images/products/' + product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                </template>

                                <h3 class="text-sm font-bold text-stone-900 group-hover:text-brand-600 transition-colors leading-snug line-clamp-2" x-text="product.name"></h3>
                                <p class="text-[11px] text-stone-400 mt-1 line-clamp-2" x-text="product.description || ''"></p>
                            </div>

                            <!-- Price Tag & Quick Add Badge -->
                            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between">
                                <span class="text-sm font-extrabold text-stone-900 font-mono-num" x-text="formatRupiah(product.price)"></span>
                                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white flex items-center justify-center text-xs transition-colors shadow-xs">
                                    <i class="fa-solid fa-plus"></i>
                                </span>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Empty State Products -->
                <div x-show="filteredProducts.length === 0" class="text-center py-16 text-stone-400">
                    <i class="fa-solid fa-utensils text-4xl mb-3 opacity-40"></i>
                    <p class="text-sm font-semibold">Tidak ada menu yang cocok</p>
                    <p class="text-xs mt-1">Coba ganti kategori atau bersihkan pencarian.</p>
                </div>
            </div>

        </section>

        <!-- KOLOM KANAN: RINGKASAN PESANAN / KERANJANG KASIR -->
        <aside 
            :class="mobileTab === 'cart' ? 'flex' : 'hidden lg:flex'"
            class="w-full lg:w-96 xl:w-[420px] bg-white h-full flex-col border-l border-stone-200 shadow-xl z-20 shrink-0">
            
            <!-- Cart Header & Reset Action -->
            <div class="p-4 border-b border-stone-200 bg-stone-50 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-stone-900 leading-tight">Keranjang Pesanan</h2>
                        <span class="text-[11px] text-stone-500 font-semibold" x-text="cartTotalCount + ' item dipilih'"></span>
                    </div>
                </div>

                <button 
                    @click="showConfirmResetModal = true" 
                    :disabled="cart.length === 0"
                    title="Kosongkan seluruh keranjang"
                    class="px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 disabled:opacity-30 disabled:pointer-events-none rounded-lg transition-colors flex items-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Reset</span>
                </button>
            </div>

            <!-- Customer & Table Input Form -->
            <div class="p-3 bg-stone-50/80 border-b border-stone-200 shrink-0 grid grid-cols-2 gap-2 text-xs">
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">No. Meja</label>
                    <div class="relative">
                        <i class="fa-solid fa-chair absolute left-2.5 top-2.5 text-stone-400 text-[10px]"></i>
                        <input 
                            type="text" 
                            x-model="tableNumber" 
                            placeholder="Meja 01 / Takeaway" 
                            class="w-full bg-white border border-stone-300 rounded-lg pl-6 pr-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-brand-500 font-semibold"
                        >
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-2.5 top-2.5 text-stone-400 text-[10px]"></i>
                        <input 
                            type="text" 
                            x-model="customerName" 
                            placeholder="Umum / Pelanggan" 
                            class="w-full bg-white border border-stone-300 rounded-lg pl-6 pr-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-brand-500 font-semibold"
                        >
                    </div>
                </div>
            </div>

            <!-- Cart Items List (Scrollable) -->
            <div class="flex-grow p-3 overflow-y-auto divide-y divide-stone-100">
                <!-- Empty Cart State -->
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-center text-stone-400 py-12 px-4">
                        <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center text-2xl text-stone-300 mb-3">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <p class="text-sm font-bold text-stone-600">Keranjang Kasir Kosong</p>
                        <p class="text-xs text-stone-400 mt-1 max-w-[200px]">Sentuh atau klik menu di sebelah kiri untuk menambahkan pesanan.</p>
                    </div>
                </template>

                <!-- Item Row -->
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="py-3 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-grow">
                                <h4 class="text-xs font-bold text-stone-900 leading-tight" x-text="item.name"></h4>
                                <span class="text-[11px] text-stone-500 font-mono-num" x-text="formatRupiah(item.price)"></span>
                            </div>

                            <!-- Subtotal item -->
                            <div class="text-right">
                                <span class="text-xs font-extrabold text-stone-900 font-mono-num" x-text="formatRupiah(item.price * item.qty)"></span>
                            </div>
                        </div>

                        <!-- Qty Controls & Note Toggle -->
                        <div class="flex items-center justify-between gap-2">
                            <!-- Note Trigger / Preview -->
                            <div class="flex-grow">
                                <template x-if="!item.showNote && !item.notes">
                                    <button 
                                        @click="item.showNote = true" 
                                        class="text-[11px] text-stone-400 hover:text-brand-600 flex items-center gap-1 transition-colors">
                                        <i class="fa-regular fa-comment-dots"></i>
                                        <span>+ Catatan</span>
                                    </button>
                                </template>
                                
                                <template x-if="!item.showNote && item.notes">
                                    <div 
                                        @click="item.showNote = true" 
                                        class="text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md cursor-pointer flex items-center gap-1 border border-amber-200/60 max-w-[170px] truncate"
                                        title="Klik untuk edit catatan">
                                        <i class="fa-solid fa-pen text-[9px]"></i>
                                        <span x-text="item.notes" class="truncate"></span>
                                    </div>
                                </template>

                                <!-- Note Input Box -->
                                <template x-if="item.showNote">
                                    <div class="flex items-center gap-1">
                                        <input 
                                            type="text" 
                                            x-model="item.notes" 
                                            placeholder="Contoh: Pedas manis / Es sedikit..." 
                                            class="w-full text-xs px-2 py-1 bg-stone-50 border border-stone-300 rounded-md focus:outline-none focus:ring-1 focus:ring-brand-500"
                                            @keydown.enter="item.showNote = false"
                                        >
                                        <button @click="item.showNote = false" class="px-1.5 py-1 text-stone-500 hover:text-stone-800 text-xs">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <!-- Qty Buttons (- 1 +) -->
                            <div class="flex items-center gap-1.5 bg-stone-100 rounded-lg p-0.5 border border-stone-200">
                                <button 
                                    @click="decreaseQty(item.id)" 
                                    class="w-6 h-6 rounded-md bg-white hover:bg-stone-200 text-stone-700 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <span class="w-6 text-center text-xs font-bold font-mono-num" x-text="item.qty"></span>
                                <button 
                                    @click="increaseQty(item.id)" 
                                    class="w-6 h-6 rounded-md bg-white hover:bg-stone-200 text-stone-700 flex items-center justify-center text-xs font-bold shadow-2xs transition-colors">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Cart Calculation Summary & Action Button -->
            <div class="p-4 bg-stone-50 border-t border-stone-200 shrink-0 space-y-3">
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between text-stone-600">
                        <span>Subtotal Makanan & Minuman</span>
                        <span class="font-bold font-mono-num text-stone-800" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-stone-500">
                        <span>Pajak Resto (0%)</span>
                        <span class="font-mono-num">Rp 0</span>
                    </div>
                    <div class="pt-2 border-t border-stone-200 flex items-center justify-between">
                        <span class="text-sm font-extrabold text-stone-900">Total Tagihan</span>
                        <span class="text-lg font-black text-brand-700 font-mono-num" x-text="formatRupiah(subtotal)"></span>
                    </div>
                </div>

                <!-- Process Payment CTA Buttons (Tunai & QRIS) -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button 
                        @click="openPaymentModal()" 
                        :disabled="cart.length === 0"
                        class="py-3 px-3 bg-stone-900 hover:bg-black disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold rounded-xl shadow-md transition-all text-xs flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-money-bill-wave text-emerald-400"></i>
                        <span>Bayar Tunai</span>
                    </button>

                    <button 
                        @click="payWithQris()" 
                        :disabled="cart.length === 0 || isProcessingQris"
                        class="py-3 px-3 bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-700 hover:to-amber-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold rounded-xl shadow-md shadow-brand-600/30 transition-all text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <template x-if="!isProcessingQris">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-qrcode text-white"></i>
                                <span>Bayar QRIS</span>
                            </span>
                        </template>
                        <template x-if="isProcessingQris">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-spinner animate-spin text-white"></i>
                                <span>Menunggu QRIS...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>

        </aside>

    </div>

    <!-- MODAL PEMBAYARAN TUNAI (CASH WORKFLOW) -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div 
            @click.away="if(!isProcessing) showPaymentModal = false" 
            class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-stone-100 space-y-5">
            
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-stone-900 leading-tight">Pembayaran Tunai (Cash)</h3>
                        <span class="text-[11px] text-stone-400 font-semibold" x-text="(tableNumber || 'Meja') + ' • ' + (customerName || 'Umum')"></span>
                    </div>
                </div>
                <button @click="if(!isProcessing) showPaymentModal = false" class="text-stone-400 hover:text-stone-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Total Yang Harus Dibayar -->
            <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200 text-center">
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider block">Total Tagihan Pesanan</span>
                <span class="text-3xl font-black text-stone-900 font-mono-num mt-1 block" x-text="formatRupiah(subtotal)"></span>
            </div>

            <!-- Input Uang Diterima & Tombol Cepat Pecahan -->
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Jumlah Uang Diterima (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-stone-400 font-bold text-sm">Rp</span>
                        <input 
                            type="number" 
                            x-model.number="cashGiven" 
                            placeholder="0" 
                            class="w-full pl-11 pr-4 py-2.5 bg-white border-2 border-stone-300 rounded-xl text-lg font-black font-mono-num text-stone-900 focus:outline-none focus:border-brand-500 transition-all"
                            @keydown.enter="processCashPayment()"
                        >
                    </div>
                </div>

                <!-- Tombol Cepat Pecahan Uang -->
                <div>
                    <span class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Pilihan Cepat:</span>
                    <div class="grid grid-cols-4 gap-2">
                        <button 
                            type="button" 
                            @click="cashGiven = subtotal" 
                            class="py-2 px-1 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-extrabold transition-all border border-stone-200/80">
                            Uang Pas
                        </button>
                        <button 
                            type="button" 
                            @click="cashGiven = 20000" 
                            class="py-2 px-1 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-extrabold transition-all border border-stone-200/80">
                            20.000
                        </button>
                        <button 
                            type="button" 
                            @click="cashGiven = 50000" 
                            class="py-2 px-1 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-extrabold transition-all border border-stone-200/80">
                            50.000
                        </button>
                        <button 
                            type="button" 
                            @click="cashGiven = 100000" 
                            class="py-2 px-1 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-extrabold transition-all border border-stone-200/80">
                            100.000
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kotak Kembalian Realtime -->
            <div 
                :class="cashGiven >= subtotal ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-red-50 border-red-200 text-red-700'"
                class="p-4 rounded-2xl border flex items-center justify-between transition-colors">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider block" x-text="cashGiven >= subtotal ? 'Uang Kembalian' : 'Uang Masih Kurang'"></span>
                    <span class="text-xl font-extrabold font-mono-num mt-0.5 block" x-text="formatRupiah(Math.abs(cashGiven - subtotal))"></span>
                </div>
                <div class="text-2xl">
                    <i :class="cashGiven >= subtotal ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-circle-exclamation text-red-500'"></i>
                </div>
            </div>

            <!-- Tombol Konfirmasi Selesai & Cetak Struk -->
            <div class="flex items-center gap-3 pt-2">
                <button 
                    type="button" 
                    @click="showPaymentModal = false" 
                    :disabled="isProcessingCash"
                    class="w-1/3 py-3 rounded-xl border border-stone-300 text-stone-600 text-xs font-bold hover:bg-stone-50 transition-colors">
                    Batal
                </button>

                <button 
                    type="button" 
                    @click="processCashPayment()" 
                    :disabled="cashGiven < subtotal || isProcessingCash"
                    class="w-2/3 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-extrabold rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                    <template x-if="!isProcessingCash">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-print"></i>
                            <span>Bayar & Cetak Nota (58mm)</span>
                        </span>
                    </template>
                    <template x-if="isProcessingCash">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            <span>Menyimpan Transaksi...</span>
                        </span>
                    </template>
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL SUKSES PEMBAYARAN QRIS / TRANSAKSI MODERN (Menggantikan Alert Default) -->
    <div 
        x-cloak 
        x-show="showSuccessModal" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-stone-900/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-stone-200 relative overflow-hidden">
            <!-- Background glow -->
            <div class="w-32 h-32 bg-emerald-100 rounded-full blur-2xl absolute -top-10 -right-10 pointer-events-none"></div>

            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-3xl mx-auto shadow-lg shadow-emerald-500/30 animate-bounce">
                <i class="fa-solid fa-check"></i>
            </div>

            <h3 class="text-xl font-extrabold text-stone-900 mt-4 tracking-tight">Pembayaran Lunas!</h3>
            <p class="text-xs text-stone-500 mt-1" x-text="successPaymentMethod === 'cash' ? 'Transaksi tunai berhasil disimpan ke dalam sistem.' : 'Transaksi QRIS berhasil diverifikasi otomatis oleh sistem Midtrans.'"></p>

            <div class="bg-stone-50 rounded-2xl p-3.5 my-4 border border-stone-100 text-left space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-stone-400 font-semibold">Nomor Nota:</span>
                    <span class="font-mono font-bold text-stone-900" x-text="successInvoiceNumber"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-400 font-semibold">Metode:</span>
                    <span class="font-bold uppercase" :class="successPaymentMethod === 'cash' ? 'text-emerald-600' : 'text-blue-600'" x-text="successPaymentMethod === 'cash' ? 'Uang Tunai (Cash)' : 'QRIS Otomatis'"></span>
                </div>
                <template x-if="successPaymentMethod === 'cash'">
                    <div class="space-y-1.5 pt-1.5 border-t border-stone-200/60">
                        <div class="flex justify-between">
                            <span class="text-stone-400 font-semibold">Total Tagihan:</span>
                            <span class="font-bold text-stone-900" x-text="formatRupiah(successTotalAmount)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-400 font-semibold">Uang Diterima:</span>
                            <span class="font-semibold text-stone-700" x-text="formatRupiah(successCashPaid)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-400 font-semibold">Kembalian:</span>
                            <span class="font-bold text-emerald-600 font-mono-num" x-text="formatRupiah(successCashChange)"></span>
                        </div>
                    </div>
                </template>
                <div class="flex justify-between pt-1 border-t border-stone-200/60">
                    <span class="text-stone-400 font-semibold">Status:</span>
                    <span class="font-bold text-emerald-600 uppercase">LUNAS (PAID)</span>
                </div>
            </div>

            <div class="space-y-2">
                <a 
                    :href="successReceiptUrl" 
                    target="_blank" 
                    class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-brand-600/20 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Nota Kasir 58mm</span>
                </a>

                <button 
                    type="button" 
                    @click="showSuccessModal = false" 
                    class="w-full py-2.5 px-4 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold rounded-xl transition-colors">
                    Selesai & Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI RESET KERANJANG MODERN -->
    <div 
        x-cloak 
        x-show="showConfirmResetModal" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-stone-200">
            <div class="w-14 h-14 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="text-lg font-bold text-stone-900">Kosongkan Keranjang?</h3>
            <p class="text-xs text-stone-500 mt-1">Semua menu pesanan yang telah dipilih akan dihapus dari layar kasir.</p>

            <div class="flex items-center gap-3 mt-6">
                <button 
                    type="button" 
                    @click="showConfirmResetModal = false" 
                    class="w-1/2 py-2.5 rounded-xl border border-stone-300 text-stone-600 text-xs font-bold hover:bg-stone-50 transition-colors">
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="cart = []; showConfirmResetModal = false; showToast('Keranjang kasir telah dikosongkan', 'info')" 
                    class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-red-600/20 transition-all">
                    Ya, Kosongkan
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION MODERN (Mulus & Non-Blocking) -->
    <div 
        x-cloak 
        x-show="toast.visible" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-xs font-bold"
        :class="{
            'bg-stone-900 text-white border-stone-800': toast.type === 'info',
            'bg-emerald-600 text-white border-emerald-500 shadow-emerald-600/20': toast.type === 'success',
            'bg-red-600 text-white border-red-500 shadow-red-600/20': toast.type === 'error',
            'bg-amber-500 text-white border-amber-400 shadow-amber-500/20': toast.type === 'warning'
        }">
        <i :class="{
            'fa-solid fa-circle-info': toast.type === 'info',
            'fa-solid fa-circle-check': toast.type === 'success',
            'fa-solid fa-triangle-exclamation': toast.type === 'error',
            'fa-solid fa-bell': toast.type === 'warning'
        }" class="text-sm"></i>
        <span x-text="toast.message"></span>
    </div>

    <!-- Alpine.js POS State Controller Script -->
    <script>
        function posApp() {
            return {
                products: <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>,
                categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>,
                selectedCategory: 'all',
                searchQuery: '',
                mobileTab: 'menu',

                // Order metadata
                tableNumber: 'Meja 01',
                customerName: 'Pelanggan',

                // Cart state
                cart: [],

                // Payment Processing States
                showPaymentModal: false,
                cashGiven: 0,
                isProcessingCash: false,
                isProcessingQris: false,
                activePollInterval: null,

                // Modern UI Enhancements (Toast & Custom Dialogs)
                showSuccessModal: false,
                successInvoiceNumber: '',
                successReceiptUrl: '',
                successPaymentMethod: 'qris',
                successTotalAmount: 0,
                successCashPaid: 0,
                successCashChange: 0,
                showConfirmResetModal: false,
                toast: {
                    visible: false,
                    message: '',
                    type: 'info',
                    timer: null
                },

                showToast(message, type = 'info') {
                    if (this.toast.timer) clearTimeout(this.toast.timer);
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.visible = true;
                    this.toast.timer = setTimeout(() => {
                        this.toast.visible = false;
                    }, 3500);
                },

                get filteredProducts() {
                    return this.products.filter(item => {
                        const matchesCategory = this.selectedCategory === 'all' || item.category_slug === this.selectedCategory;
                        const query = this.searchQuery.trim().toLowerCase();
                        const matchesSearch = query === '' || 
                                              item.name.toLowerCase().includes(query) || 
                                              (item.description && item.description.toLowerCase().includes(query));
                        return matchesCategory && matchesSearch;
                    });
                },

                get cartTotalCount() {
                    return this.cart.reduce((total, item) => total + item.qty, 0);
                },

                get subtotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.qty), 0);
                },

                getItemQtyInCart(productId) {
                    const item = this.cart.find(c => c.id === productId);
                    return item ? item.qty : 0;
                },

                addToCart(product) {
                    const existing = this.cart.find(c => c.id === product.id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: Number(product.price),
                            qty: 1,
                            notes: '',
                            showNote: false
                        });
                    }
                },

                increaseQty(productId) {
                    const item = this.cart.find(c => c.id === productId);
                    if (item) {
                        item.qty++;
                    }
                },

                decreaseQty(productId) {
                    const index = this.cart.findIndex(c => c.id === productId);
                    if (index !== -1) {
                        if (this.cart[index].qty > 1) {
                            this.cart[index].qty--;
                        } else {
                            this.cart.splice(index, 1);
                        }
                    }
                },

                resetCart() {
                    if (confirm('Kosongkan semua item dalam keranjang kasir?')) {
                        this.cart = [];
                    }
                },

                openPaymentModal() {
                    if (this.cart.length === 0) return;
                    this.cashGiven = this.subtotal; // default pas
                    this.showPaymentModal = true;
                },

                async processCashPayment() {
                    if (this.cashGiven < this.subtotal || this.isProcessingCash) return;

                    this.isProcessingCash = true;

                    const payload = {
                        customer_name: this.customerName,
                        table_number: this.tableNumber,
                        total_amount: this.subtotal,
                        cash_paid: this.cashGiven,
                        items: this.cart
                    };

                    try {
                        const response = await fetch('/api/orders/cash', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        const res = await response.json();

                        if (res.success) {
                            // Tutup modal input uang kas
                            this.showPaymentModal = false;

                            // Simpan detail pembayaran untuk modal sukses
                            this.successInvoiceNumber = res.invoice_number;
                            this.successReceiptUrl = res.receipt_url;
                            this.successPaymentMethod = 'cash';
                            this.successTotalAmount = res.total_amount;
                            this.successCashPaid = res.cash_paid;
                            this.successCashChange = res.cash_change;

                            // Bersihkan keranjang kasir
                            this.cart = [];
                            this.cashGiven = 0;
                            this.customerName = 'Pelanggan';

                            // Tampilkan Modal Sukses Pembayaran Modern & Toast
                            this.showSuccessModal = true;
                            this.showToast('Transaksi tunai berhasil disimpan!', 'success');

                            // Kembali ke tab menu di mobile jika sebelumnya di cart
                            this.mobileTab = 'menu';
                        } else {
                            this.showToast('Gagal menyimpan transaksi: ' + (res.error || 'Terjadi kesalahan sistem.'), 'error');
                        }
                    } catch (err) {
                        this.showToast('Koneksi terputus ke server.', 'error');
                    } finally {
                        this.isProcessingCash = false;
                    }
                },

                async payWithQris() {
                    if (this.cart.length === 0 || this.isProcessingQris) return;

                    if (typeof window.snap === 'undefined') {
                        this.showToast('Midtrans Snap SDK belum termuat. Pastikan koneksi internet aktif.', 'warning');
                        return;
                    }

                    this.isProcessingQris = true;

                    // Hentikan polling lama jika ada
                    this.stopStatusPolling();

                    const payload = {
                        customer_name: this.customerName,
                        table_number: this.tableNumber,
                        total_amount: this.subtotal,
                        items: this.cart
                    };

                    try {
                        const response = await fetch('/api/payment/create', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        const res = await response.json();

                        if (!res.success) {
                            this.showToast('Gagal membuat transaksi QRIS: ' + (res.error || 'Server Key belum diatur.'), 'error');
                            this.isProcessingQris = false;
                            return;
                        }

                        const self = this;
                        const invoiceNumber = res.invoice_number;

                        // Buka Pop-up Snap Midtrans untuk Scan QRIS
                        window.snap.pay(res.snap_token, {
                            onSuccess: function(result) {
                                self.stopStatusPolling();
                                self.onPaymentSettled(invoiceNumber);
                            },
                            onPending: function(result) {
                                // Mulai polling otomatis mendeteksi ketika pelanggan selesai scan
                                self.showToast('Menunggu pembayaran QRIS dari pelanggan...', 'info');
                                self.startStatusPolling(invoiceNumber);
                            },
                            onError: function(result) {
                                self.stopStatusPolling();
                                self.showToast('Pembayaran QRIS gagal atau dibatalkan.', 'error');
                                self.isProcessingQris = false;
                            },
                            onClose: function() {
                                // Kasir menutup popup Snap dengan tombol [X] -> reset tombol agar bisa diklik kembali
                                self.stopStatusPolling();
                                self.isProcessingQris = false;
                                self.showToast('Pembayaran QRIS ditutup.', 'info');
                            }
                        });

                    } catch (err) {
                        this.showToast('Terjadi kesalahan koneksi ke server.', 'error');
                        this.isProcessingQris = false;
                    }
                },

                stopStatusPolling() {
                    if (this.activePollInterval) {
                        clearInterval(this.activePollInterval);
                        this.activePollInterval = null;
                    }
                },

                startStatusPolling(invoiceNumber) {
                    this.stopStatusPolling();

                    let attempts = 0;
                    const maxAttempts = 60; // Poll selama 3 menit (setiap 3 detik)
                    const self = this;

                    this.activePollInterval = setInterval(async () => {
                        attempts++;
                        if (attempts > maxAttempts) {
                            self.stopStatusPolling();
                            self.isProcessingQris = false;
                            return;
                        }

                        try {
                            const res = await fetch('/api/payment/status/' + invoiceNumber);
                            const data = await res.json();

                            if (data.success && data.payment_status === 'paid') {
                                self.stopStatusPolling();
                                self.onPaymentSettled(invoiceNumber);
                            }
                        } catch (e) {
                            console.error('Polling error:', e);
                        }
                    }, 3000);
                },

                async onPaymentSettled(invoiceNumber) {
                    try {
                        const res = await fetch('/api/payment/status/' + invoiceNumber);
                        const data = await res.json();

                        if (data.success) {
                            // Bersihkan keranjang kasir
                            this.cart = [];
                            this.customerName = 'Pelanggan';
                            this.mobileTab = 'menu';

                            // Buka Modal Sukses Modern
                            this.successInvoiceNumber = invoiceNumber;
                            this.successReceiptUrl = data.receipt_url;
                            this.successPaymentMethod = 'qris';
                            this.successTotalAmount = data.order ? data.order.total_amount : 0;
                            this.showSuccessModal = true;
                            this.showToast('Pembayaran QRIS LUNAS!', 'success');
                        }
                    } catch (e) {
                        console.error('Settled error:', e);
                    } finally {
                        this.isProcessingQris = false;
                    }
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                },

                // PWA & Low-Device / Dexie.js Integration
                deferredPrompt: null,
                isFullscreen: false,
                indexedDbInstance: null,

                async init() {
                    this.initIndexedDb();
                    this.setupPwaEvents();
                },

                async initIndexedDb() {
                    try {
                        if (typeof Dexie !== 'undefined') {
                            this.indexedDbInstance = new Dexie('KangNaryoPOS_DB');
                            this.indexedDbInstance.version(1).stores({
                                products: 'id, category_slug, name, price, is_available',
                                categories: 'id, slug, name'
                            });

                            // Jika offline atau jaringan lambat, baca dulu dari IndexedDB
                            const cachedCount = await this.indexedDbInstance.products.count();
                            if (cachedCount > 0 && (!this.products || this.products.length === 0)) {
                                this.products = await this.indexedDbInstance.products.toArray();
                                this.categories = await this.indexedDbInstance.categories.toArray();
                                console.log('[Dexie.js] Menu dimuat seketika dari IndexedDB lokal (' + cachedCount + ' item)');
                            }

                            // Sync dan perbarui IndexedDB dengan data terbaru
                            if (this.products && this.products.length > 0) {
                                await this.indexedDbInstance.products.clear();
                                await this.indexedDbInstance.products.bulkPut(this.products);
                                await this.indexedDbInstance.categories.clear();
                                await this.indexedDbInstance.categories.bulkPut(this.categories);
                                console.log('[Dexie.js] IndexedDB berhasil disinkronisasi & di-cache.');
                            }
                        }
                    } catch (e) {
                        console.warn('[Dexie.js] IndexedDB fallback warning:', e);
                    }
                },

                setupPwaEvents() {
                    // Tangkap event instalasi browser PWA
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        console.log('[PWA] beforeinstallprompt ditangkap, tombol pasang siap ditampilkan.');
                    });

                    window.addEventListener('appinstalled', () => {
                        this.deferredPrompt = null;
                        console.log('[PWA] Aplikasi POS berhasil dipasang di perangkat.');
                    });
                },

                async promptInstallApp() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        const { outcome } = await this.deferredPrompt.userChoice;
                        console.log('[PWA] Pilihan user install:', outcome);
                        this.deferredPrompt = null;
                    }
                },

                toggleFullScreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().then(() => {
                            this.isFullscreen = true;
                        }).catch(err => {
                            console.warn('Fullscreen error:', err);
                        });
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen().then(() => {
                                this.isFullscreen = false;
                            });
                        }
                    }
                }
            };
        }
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('[PWA] Service Worker terdaftar (POS):', reg.scope))
                    .catch((err) => console.warn('[PWA] Service Worker gagal daftar:', err));
            });
        }
    </script>
</body>
</html>
