<!-- Admin Dashboard Overview -->
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Ringkasan Restoran</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Status database produk, kategori, dan pesanan terkini.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/admin/products" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-brand-600/20 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Kelola Menu</span>
            </a>
            <a href="/admin/categories" class="inline-flex items-center gap-2 px-4 py-2 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs sm:text-sm font-bold transition-all">
                <i class="fa-solid fa-tags"></i>
                <span>Kategori</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider">Total Menu</span>
                <p class="text-2xl font-extrabold text-stone-900 mt-1"><?= count($products) ?></p>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">
                    <?= count(array_filter($products, fn($p) => $p['is_available'] == 1)) ?> Tersedia
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-utensils"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider">Menu Habis</span>
                <p class="text-2xl font-extrabold text-red-600 mt-1">
                    <?= count(array_filter($products, fn($p) => $p['is_available'] == 0)) ?>
                </p>
                <span class="text-[11px] text-stone-400 mt-1 inline-block">Perlu restok bahan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider">Kategori</span>
                <p class="text-2xl font-extrabold text-stone-900 mt-1"><?= count($categories) ?></p>
                <span class="text-[11px] text-stone-400 mt-1 inline-block">Grup sajian</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider">Database</span>
                <p class="text-lg font-extrabold text-stone-900 mt-1">SQLite 3</p>
                <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">Online & Connected</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-database"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="bg-gradient-to-r from-stone-900 to-stone-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Issue #3 Terimplementasi</span>
            <h2 class="text-lg font-bold mt-1">Manajemen Produk & Kategori dengan Toggle Instan</h2>
            <p class="text-xs text-stone-300 mt-0.5 max-w-xl">
                Ubah ketersediaan menu secara langsung. Perubahan switch "Tersedia / Habis" akan langsung berpengaruh seketika ke halaman buku menu digital publik dan kasir POS.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/products" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                Kelola Produk Sekarang
            </a>
        </div>
    </div>
</div>
