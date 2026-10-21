<!-- Digital Menu Catalog Page -->
<div x-data="{
    searchQuery: '',
    selectedCategory: 'all',
    items: <?= htmlspecialchars(json_encode($products, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>,
    categories: <?= htmlspecialchars(json_encode($categories, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>,

    get filteredItems() {
        return this.items.filter(item => {
            const matchesCat = this.selectedCategory === 'all' || item.category_slug === this.selectedCategory;
            const query = this.searchQuery.trim().toLowerCase();
            const matchesSearch = query === '' || 
                                  item.name.toLowerCase().includes(query) || 
                                  (item.description && item.description.toLowerCase().includes(query));
            return matchesCat && matchesSearch;
        });
    },

    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }
}" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

    <!-- Top Header & QR Code Info Banner -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-stone-900 to-stone-800 text-white p-6 sm:p-8 rounded-3xl shadow-lg relative overflow-hidden">
            <!-- Background Decoration Pattern -->
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
                <i class="fa-solid fa-utensils"></i>
            </div>

            <div class="space-y-2 z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                    <i class="fa-solid fa-qrcode"></i> Akses Meja Pelanggan
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Katalog Buku Menu Digital</h1>
                <p class="text-xs sm:text-sm text-stone-300 max-w-xl">
                    Pilih menu favorit Anda langsung dari smartphone. Seluruh harga sudah transparan dan terupdate realtime.
                </p>
            </div>

            <div class="flex items-center gap-3 z-10 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/10 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-amber-400 text-stone-950 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
                <div>
                    <span class="block text-[11px] text-stone-300 font-semibold uppercase tracking-wider">Cara Pesan</span>
                    <span class="text-xs font-bold text-white">Catat nomor menu / pesan ke pelayan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Filter & Search Control Panel -->
    <div class="sticky top-16 z-30 bg-stone-50/95 backdrop-blur-md py-4 mb-6 border-b border-stone-200/80 -mx-4 px-4 sm:mx-0 sm:px-0 transition-all">
        <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            
            <!-- Category Tabs (Horizontal Scrollable on Mobile) -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0">
                <button 
                    @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' 
                        ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
                        : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200'"
                    class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-border-all text-xs"></i>
                    <span>Semua Menu</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-full" :class="selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-500'" x-text="items.length"></span>
                </button>

                <template x-for="cat in categories" :key="cat.id">
                    <button 
                        @click="selectedCategory = cat.slug"
                        :class="selectedCategory === cat.slug 
                            ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' 
                            : 'bg-white text-stone-600 hover:bg-stone-100 border border-stone-200'"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold whitespace-nowrap transition-all duration-200 cursor-pointer flex items-center gap-2">
                        <i :class="cat.icon ? 'fa-solid ' + cat.icon : 'fa-solid fa-utensils'" class="text-xs"></i>
                        <span x-text="cat.name"></span>
                    </button>
                </template>
            </div>

            <!-- Search Input with Alpine.js -->
            <div class="relative w-full sm:w-72 shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    placeholder="Cari menu, ayam, jus..." 
                    class="w-full pl-9 pr-8 py-2 bg-white border border-stone-300 rounded-xl text-xs sm:text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm transition-all"
                >
                <button 
                    x-show="searchQuery.length > 0" 
                    @click="searchQuery = ''"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-600"
                    title="Hapus pencarian">
                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Active Filter Counter & Helper -->
    <div class="flex items-center justify-between text-xs text-stone-500 mb-6">
        <div>
            Menampilkan <span class="font-bold text-stone-800" x-text="filteredItems.length"></span> menu
            <span x-show="selectedCategory !== 'all'" class="text-brand-600 font-semibold">
                (kategori dipilih)
            </span>
            <span x-show="searchQuery.trim() !== ''">
                untuk kata kunci "<span class="font-bold text-stone-800" x-text="searchQuery"></span>"
            </span>
        </div>
        <div class="hidden sm:flex items-center gap-3 text-[11px]">
            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Favorit</span>
            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Habis</span>
        </div>
    </div>

    <!-- Menu Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-show="filteredItems.length > 0">
        <template x-for="item in filteredItems" :key="item.id">
            <div 
                :class="item.is_available == 0 ? 'opacity-85 border-stone-200 bg-stone-50' : 'bg-white border-stone-200 hover:border-brand-400 hover:shadow-lg'"
                class="group rounded-2xl border shadow-sm transition-all duration-300 flex flex-col justify-between overflow-hidden relative">
                
                <!-- Card Header & Badges -->
                <div class="p-5">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-stone-100 text-stone-700" x-text="item.category_name || 'Menu'">
                        </span>
                        
                        <!-- Badges Status -->
                        <div class="flex items-center gap-1.5">
                            <!-- Out of Stock Badge -->
                            <template x-if="item.is_available == 0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold border border-red-200 shadow-xs">
                                    <i class="fa-solid fa-circle-xmark text-[11px]"></i> Habis
                                </span>
                            </template>
                            
                            <!-- Favorite Badge -->
                            <template x-if="item.is_featured == 1 && item.is_available == 1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200">
                                    <i class="fa-solid fa-star text-[10px] text-amber-500"></i> Favorit
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Item Name -->
                    <h2 
                        :class="item.is_available == 0 ? 'text-stone-600 line-through' : 'text-stone-900 group-hover:text-brand-600'"
                        class="text-lg font-bold transition-colors leading-snug"
                        x-text="item.name">
                    </h2>

                    <!-- Item Description -->
                    <p class="text-xs sm:text-sm text-stone-500 mt-2 leading-relaxed" x-text="item.description || 'Pilihan lezat dengan bumbu rempah pilihan khas Kang Naryo.'">
                    </p>
                </div>

                <!-- Card Footer & Pricing -->
                <div class="px-5 py-4 bg-stone-50/70 border-t border-stone-100 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-stone-400 block font-medium">Harga Menu</span>
                        <span 
                            :class="item.is_available == 0 ? 'text-stone-500' : 'text-brand-700'"
                            class="text-base font-extrabold" 
                            x-text="formatRupiah(item.price)">
                        </span>
                    </div>

                    <div>
                        <!-- Ready Button (Non-clickable notice or order CTA) -->
                        <template x-if="item.is_available == 1">
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-xl bg-brand-50 text-brand-700 border border-brand-200">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-[11px]"></i> Siap Saji
                            </span>
                        </template>

                        <!-- Unavailable Notice -->
                        <template x-if="item.is_available == 0">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-xl bg-stone-200 text-stone-500 cursor-not-allowed">
                                Tidak Tersedia
                            </span>
                        </template>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Empty State when filter/search finds nothing -->
    <div x-show="filteredItems.length === 0" x-cloak class="text-center py-16 bg-white rounded-3xl border border-stone-200 p-8 shadow-sm my-6">
        <div class="w-16 h-16 rounded-full bg-stone-100 text-stone-400 mx-auto flex items-center justify-center text-2xl mb-4">
            <i class="fa-solid fa-bowl-food"></i>
        </div>
        <h3 class="text-lg font-bold text-stone-800">Menu tidak ditemukan</h3>
        <p class="text-sm text-stone-500 max-w-sm mx-auto mt-1">
            Maaf, tidak ada menu yang cocok dengan pencarian "<span class="font-semibold text-stone-700" x-text="searchQuery"></span>".
        </p>
        <button 
            @click="searchQuery = ''; selectedCategory = 'all'" 
            class="mt-4 px-4 py-2 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-700 transition-colors shadow-sm">
            Reset Pencarian & Kategori
        </button>
    </div>

</div>
