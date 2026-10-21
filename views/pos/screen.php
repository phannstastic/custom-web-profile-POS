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
    <?php endif; ?>

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

        <!-- Header Actions (Full Screen, Admin Link, Cart badge for Mobile) -->
        <div class="flex items-center gap-2">
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
                            class="group bg-white rounded-2xl border border-stone-200/90 shadow-xs hover:shadow-md hover:border-brand-500 active:scale-95 transition-all duration-150 p-3.5 flex flex-col justify-between cursor-pointer select-none">
                            
                            <!-- Product Visual / Icon -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 bg-stone-100 px-2 py-0.5 rounded-md line-clamp-1" x-text="product.category_name || 'Menu'"></span>
                                    <template x-if="getItemQtyInCart(product.id) > 0">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-600 text-white text-[11px] font-extrabold shadow-sm" x-text="getItemQtyInCart(product.id)"></span>
                                    </template>
                                </div>
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
                    @click="resetCart()" 
                    :disabled="cart.length === 0"
                    title="Kosongkan seluruh keranjang"
                    class="px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 disabled:opacity-30 disabled:pointer-events-none rounded-lg transition-colors flex items-center gap-1">
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

                <!-- Process Payment CTA Button -->
                <button 
                    @click="proceedToPayment()" 
                    :disabled="cart.length === 0"
                    class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold rounded-xl shadow-lg shadow-brand-600/30 hover:shadow-xl transition-all text-sm flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>Lanjut ke Pembayaran</span>
                    </span>
                    <span class="font-mono-num text-sm bg-white/20 px-2 py-0.5 rounded-lg" x-text="formatRupiah(subtotal)"></span>
                </button>
            </div>

        </aside>

    </div>

    <!-- Alpine.js POS State Controller Script -->
    <script>
        function posApp() {
            return {
                products: <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>,
                categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>,
                selectedCategory: 'all',
                searchQuery: '',
                mobileTab: 'menu', // 'menu' or 'cart'

                // Order metadata
                tableNumber: 'Meja 01',
                customerName: 'Pelanggan',

                // Cart state
                cart: [],

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
                            // Hapus jika sudah 1 lalu dikurangi
                            this.cart.splice(index, 1);
                        }
                    }
                },

                resetCart() {
                    if (confirm('Kosongkan semua item dalam keranjang kasir?')) {
                        this.cart = [];
                    }
                },

                proceedToPayment() {
                    if (this.cart.length === 0) return;
                    alert('Keranjang siap! Total: ' + this.formatRupiah(this.subtotal) + '\nWorkflow pembayaran Tunai & Cetak Struk 58mm akan diimplementasikan pada Issue #5.');
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                }
            };
        }
    </script>
</body>
</html>
