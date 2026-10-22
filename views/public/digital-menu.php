<!-- Digital Menu Catalog Page -->
<div x-data="digitalMenuApp()" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

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
                    Sentuh atau klik menu untuk melihat foto resolusi tinggi, galeri masakan, serta rincian lengkapnya.
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
            <span class="inline-flex items-center gap-1 text-stone-400"><i class="fa-regular fa-hand-pointer"></i> Klik foto untuk perbesar</span>
        </div>
    </div>

    <!-- Menu Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-show="filteredItems.length > 0">
        <template x-for="item in filteredItems" :key="item.id">
            <div 
                @click="openModal(item)"
                :class="item.is_available == 0 ? 'opacity-85 border-stone-200 bg-stone-50' : 'bg-white border-stone-200 hover:border-brand-400 hover:shadow-xl hover:-translate-y-1'"
                class="group rounded-3xl border shadow-sm transition-all duration-300 flex flex-col justify-between overflow-hidden relative cursor-pointer select-none">
                
                <!-- Product Card Image Box -->
                <div class="relative w-full h-48 bg-stone-100 overflow-hidden">
                    <template x-if="item.image">
                        <img 
                            :src="'/assets/images/products/' + item.image" 
                            :alt="item.name" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                    </template>
                    <template x-if="!item.image">
                        <div class="w-full h-full flex flex-col items-center justify-center text-stone-300 bg-stone-50">
                            <i class="fa-solid fa-utensils text-4xl mb-2"></i>
                            <span class="text-xs font-semibold text-stone-400">Pondok Makan Kang Naryo</span>
                        </div>
                    </template>

                    <!-- Gradient Overlay on Hover -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-60 group-hover:opacity-40 transition-opacity"></div>

                    <!-- Top Badges -->
                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 z-10">
                        <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-xl bg-white/90 backdrop-blur-md text-stone-800 shadow-sm" x-text="item.category_name || 'Menu'">
                        </span>
                        
                        <div class="flex items-center gap-1.5">
                            <!-- Out of Stock Badge -->
                            <template x-if="item.is_available == 0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-600 text-white text-xs font-extrabold shadow-md">
                                    <i class="fa-solid fa-circle-xmark text-[11px]"></i> Habis
                                </span>
                            </template>
                            
                            <!-- Favorite Badge -->
                            <template x-if="item.is_featured == 1 && item.is_available == 1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-400 text-stone-950 text-xs font-extrabold shadow-md">
                                    <i class="fa-solid fa-star text-[10px]"></i> Favorit
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Slide Hint Overlay -->
                    <div class="absolute bottom-3 right-3 z-10">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold">
                            <i class="fa-solid fa-images text-[10px]"></i>
                            <span>Galeri Foto</span>
                        </span>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-5 flex-grow flex flex-col justify-between">
                    <div>
                        <!-- Item Name -->
                        <h2 
                            :class="item.is_available == 0 ? 'text-stone-600 line-through' : 'text-stone-900 group-hover:text-brand-600'"
                            class="text-lg font-bold transition-colors leading-snug"
                            x-text="item.name">
                        </h2>

                        <!-- Item Description -->
                        <p class="text-xs sm:text-sm text-stone-500 mt-2 leading-relaxed line-clamp-2" x-text="item.description || 'Pilihan lezat dengan bumbu rempah pilihan khas Kang Naryo.'">
                        </p>
                    </div>

                    <!-- Price & Action Trigger -->
                    <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-stone-400 block font-semibold uppercase tracking-wider">Harga Menu</span>
                            <span 
                                :class="item.is_available == 0 ? 'text-stone-400' : 'text-brand-600'"
                                class="text-lg font-extrabold font-mono-num" 
                                x-text="formatRupiah(item.price)">
                            </span>
                        </div>

                        <div>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-stone-100 group-hover:bg-brand-600 group-hover:text-white text-stone-700 transition-colors shadow-xs">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </span>
                        </div>
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

    <!-- MODAL POPUP DETAIL MENU DENGAN CAROUSEL FOTO SLIDE -->
    <div 
        x-cloak 
        x-show="isModalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-stone-950/80 backdrop-blur-md"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @keydown.escape.window="closeModal()">
        
        <div 
            @click.away="closeModal()" 
            class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-stone-200 relative flex flex-col max-h-[90vh]">
            
            <!-- Modal Close Floating Button -->
            <button 
                type="button" 
                @click="closeModal()" 
                class="absolute top-4 right-4 z-30 w-9 h-9 rounded-full bg-stone-900/70 hover:bg-stone-900 text-white flex items-center justify-center transition-all shadow-lg backdrop-blur-sm">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Carousel Image Slider Container -->
            <div class="relative w-full h-64 sm:h-72 bg-stone-900 shrink-0 select-none overflow-hidden group">
                <!-- Slide Images -->
                <template x-for="(imgSrc, idx) in activeSlides" :key="idx">
                    <div 
                        x-show="currentSlideIndex === idx" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-105"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 w-full h-full">
                        <img :src="'/assets/images/products/' + imgSrc" :alt="activeItem?.name" class="w-full h-full object-cover">
                    </div>
                </template>

                <!-- Gradient Shadow on Slider -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 pointer-events-none"></div>

                <!-- Slide Navigation Arrows (muncul jika gambar > 1) -->
                <template x-if="activeSlides.length > 1">
                    <div>
                        <!-- Prev Button -->
                        <button 
                            type="button" 
                            @click="prevSlide()" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-stone-900 flex items-center justify-center shadow-lg transition-transform active:scale-90">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <!-- Next Button -->
                        <button 
                            type="button" 
                            @click="nextSlide()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-stone-900 flex items-center justify-center shadow-lg transition-transform active:scale-90">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </template>

                <!-- Slide Indicator Dots -->
                <template x-if="activeSlides.length > 1">
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-full">
                        <template x-for="(img, idx) in activeSlides" :key="idx">
                            <button 
                                type="button" 
                                @click="currentSlideIndex = idx" 
                                :class="currentSlideIndex === idx ? 'w-5 bg-amber-400' : 'w-2 bg-white/60'" 
                                class="h-2 rounded-full transition-all duration-200">
                            </button>
                        </template>
                    </div>
                </template>

                <!-- Category & Slide Count Badge on Image -->
                <div class="absolute top-4 left-4 z-20 flex items-center gap-2">
                    <span class="px-3 py-1 rounded-xl bg-white/90 backdrop-blur-md text-stone-900 text-xs font-bold uppercase shadow-sm" x-text="activeItem?.category_name || 'Menu'"></span>
                    <span x-show="activeSlides.length > 1" class="px-2.5 py-1 rounded-xl bg-black/60 text-white text-[11px] font-semibold">
                        <span x-text="(currentSlideIndex + 1)"></span>/<span x-text="activeSlides.length"></span> Foto
                    </span>
                </div>
            </div>

            <!-- Modal Content & Details (Scrollable if needed) -->
            <div class="p-6 overflow-y-auto space-y-4">
                
                <!-- Title & Stock Status -->
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-stone-900 leading-tight" x-text="activeItem?.name"></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <template x-if="activeItem?.is_available == 1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Siap Dipesan
                                </span>
                            </template>
                            <template x-if="activeItem?.is_available == 0">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-[11px] font-bold">
                                    <i class="fa-solid fa-circle-xmark"></i> Stok Sedang Habis
                                </span>
                            </template>
                            <template x-if="activeItem?.is_featured == 1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">
                                    <i class="fa-solid fa-star text-amber-500"></i> Menu Favorit
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Price Badge -->
                    <div class="text-right shrink-0">
                        <span class="text-[10px] text-stone-400 block uppercase font-bold tracking-wider">Harga Menu</span>
                        <span class="text-xl sm:text-2xl font-black text-brand-600 font-mono-num" x-text="formatRupiah(activeItem?.price || 0)"></span>
                    </div>
                </div>

                <!-- Description & Flavor Notes -->
                <div class="bg-stone-50 p-4 rounded-2xl border border-stone-100 space-y-2">
                    <span class="text-xs font-bold text-stone-700 block uppercase tracking-wider">Keterangan Menu:</span>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed" x-text="activeItem?.description || 'Menu lezat dibuat dari bahan segar bermutu tinggi dengan racikan bumbu khas tradisional.'"></p>
                </div>

                <!-- Thumbnails Bar (Click to Switch Slide) -->
                <template x-if="activeSlides.length > 1">
                    <div>
                        <span class="text-xs font-bold text-stone-700 block mb-2">Pilihan Foto Menu:</span>
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            <template x-for="(imgSrc, idx) in activeSlides" :key="idx">
                                <button 
                                    type="button" 
                                    @click="currentSlideIndex = idx"
                                    :class="currentSlideIndex === idx ? 'ring-2 ring-brand-600 scale-105' : 'opacity-70 hover:opacity-100'"
                                    class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border border-stone-200 transition-all cursor-pointer">
                                    <img :src="'/assets/images/products/' + imgSrc" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-stone-50 border-t border-stone-100 flex items-center justify-between gap-3">
                <span class="text-[11px] text-stone-400 font-medium">Buku Menu Digital • Pondok Makan Kang Naryo</span>
                <button 
                    type="button" 
                    @click="closeModal()" 
                    class="px-5 py-2.5 bg-stone-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-md">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>

<script>
function digitalMenuApp() {
    return {
        searchQuery: '',
        selectedCategory: 'all',
        items: <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>,
        categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>,

        // Modal & Carousel state
        isModalOpen: false,
        activeItem: null,
        activeSlides: [],
        currentSlideIndex: 0,

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
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        },

        openModal(item) {
            this.activeItem = item;
            this.currentSlideIndex = 0;

            // Kumpulkan slide gambar dari gallery_images atau image utama
            let slides = [];
            if (item.gallery_images) {
                try {
                    const parsed = typeof item.gallery_images === 'string' ? JSON.parse(item.gallery_images) : item.gallery_images;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        slides = parsed;
                    }
                } catch (e) {
                    console.warn('Error parsing gallery_images:', e);
                }
            }

            if (slides.length === 0 && item.image) {
                slides = [item.image];
            }

            this.activeSlides = slides;
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
            this.activeItem = null;
            this.activeSlides = [];
            this.currentSlideIndex = 0;
        },

        nextSlide() {
            if (this.activeSlides.length > 1) {
                this.currentSlideIndex = (this.currentSlideIndex + 1) % this.activeSlides.length;
            }
        },

        prevSlide() {
            if (this.activeSlides.length > 1) {
                this.currentSlideIndex = (this.currentSlideIndex - 1 + this.activeSlides.length) % this.activeSlides.length;
            }
        }
    };
}
</script>
