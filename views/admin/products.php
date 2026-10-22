<!-- Product Management Page -->
<div x-data="{
    showModal: false,
    isEdit: false,
    formId: '',
    formName: '',
    formCategoryId: '',
    formPrice: '',
    formDescription: '',
    formIsAvailable: 1,
    formIsFeatured: 0,
    searchFilter: '',

    openAddModal() {
        this.isEdit = false;
        this.formId = '';
        this.formName = '';
        this.formCategoryId = '<?= !empty($categories[0]['id']) ? $categories[0]['id'] : '' ?>';
        this.formPrice = '';
        this.formDescription = '';
        this.formIsAvailable = 1;
        this.formIsFeatured = 0;
        this.showModal = true;
    },

    openEditModal(item) {
        this.isEdit = true;
        this.formId = item.id;
        this.formName = item.name;
        this.formCategoryId = item.category_id || '';
        this.formPrice = item.price;
        this.formDescription = item.description || '';
        this.formIsAvailable = item.is_available;
        this.formIsFeatured = item.is_featured;
        this.showModal = true;
    }
}" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Menu & Produk</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Atur ketersediaan menu, harga, status favorit, dan foto sajian.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-brand-600/20 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </button>
    </div>

    <!-- Product Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-stone-200/80 bg-stone-50 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-stone-400 text-xs"></i>
                <input 
                    type="text" 
                    x-model="searchFilter" 
                    placeholder="Cari nama menu..." 
                    class="w-full pl-9 pr-3 py-1.5 bg-white border border-stone-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
            </div>
            <div class="text-xs text-stone-500 font-semibold">
                Total: <?= count($products) ?> Menu
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-600">
                <thead class="bg-stone-50 text-stone-700 text-xs uppercase tracking-wider font-bold border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4">Foto & Menu</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga (Rp)</th>
                        <th class="px-6 py-4 text-center">Status Stok</th>
                        <th class="px-6 py-4 text-center">Favorit</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-stone-400 text-sm">
                                Belum ada menu makanan / minuman.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $item): ?>
                            <tr 
                                x-show="!searchFilter || '<?= strtolower(addslashes($item['name'])) ?>'.includes(searchFilter.toLowerCase())"
                                class="hover:bg-stone-50 transition-colors">
                                <!-- Menu Name & Photo -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-stone-100 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center text-stone-400">
                                            <?php if (!empty($item['image']) && file_exists(dirname(__DIR__, 2) . '/public/assets/images/products/' . $item['image'])): ?>
                                                <img src="/assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <i class="fa-solid fa-bowl-food text-lg"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-stone-900 leading-tight"><?= htmlspecialchars($item['name']) ?></p>
                                            <p class="text-xs text-stone-400 line-clamp-1 max-w-xs mt-0.5"><?= htmlspecialchars($item['description'] ?? '-') ?></p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-6 py-4">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-md bg-stone-100 text-stone-700">
                                        <?= htmlspecialchars($item['category_name'] ?? 'Umum') ?>
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="px-6 py-4 font-bold text-stone-900">
                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                </td>

                                <!-- Availability Toggle -->
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="/admin/products/toggle-availability">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button 
                                            type="submit" 
                                            title="Klik untuk ubah ketersediaan"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-xs <?= $item['is_available'] == 1 ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200' ?>">
                                            <span class="w-2 h-2 rounded-full <?= $item['is_available'] == 1 ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                                            <span><?= $item['is_available'] == 1 ? 'Tersedia' : 'Habis' ?></span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Featured Toggle -->
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="/admin/products/toggle-featured">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button 
                                            type="submit" 
                                            title="Klik untuk ubah status favorit"
                                            class="p-2 rounded-lg text-sm transition-colors <?= $item['is_featured'] == 1 ? 'text-amber-500 hover:text-amber-600 bg-amber-50' : 'text-stone-300 hover:text-stone-400' ?>">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button 
                                            @click="openEditModal(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="p-2 text-stone-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Edit Menu">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form method="POST" action="/admin/products/delete" onsubmit="return confirm('Hapus menu <?= htmlspecialchars(addslashes($item['name'])) ?> secara permanen?');">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Menu">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form (Tambah / Edit Produk) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div 
            @click.away="showModal = false" 
            class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-stone-100 space-y-5">
            
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-lg font-bold text-stone-900" x-text="isEdit ? 'Edit Data Menu' : 'Tambah Menu Baru'"></h3>
                <button @click="showModal = false" class="text-stone-400 hover:text-stone-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" :action="isEdit ? '/admin/products/update' : '/admin/products/create'" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id" :value="formId" x-show="isEdit">

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Menu *</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="formName" 
                        required 
                        placeholder="Contoh: Ayam Bakar Madu Spesial" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Kategori *</label>
                        <select 
                            name="category_id" 
                            x-model="formCategoryId" 
                            required 
                            class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Harga Jual (Rp) *</label>
                        <input 
                            type="number" 
                            name="price" 
                            x-model="formPrice" 
                            required 
                            placeholder="Contoh: 25000" 
                            class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <textarea 
                        name="description" 
                        x-model="formDescription" 
                        rows="2" 
                        placeholder="Keterangan bahan / cita rasa menu..." 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"></textarea>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Foto Utama Menu (Cover)</label>
                        <input 
                            type="file" 
                            name="image" 
                            accept="image/jpeg,image/png,image/webp" 
                            class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100"
                        >
                        <span class="text-[11px] text-stone-400 mt-1 block">Foto sampul yang muncul di daftar menu kartu depan & kasir POS.</span>
                    </div>

                    <div class="p-3.5 bg-stone-50 rounded-xl border border-stone-200">
                        <label class="block text-xs font-bold text-stone-800 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-images text-brand-600 mr-1"></i> Upload Banyak Foto Sekaligus (Galeri Slide)
                        </label>
                        <input 
                            type="file" 
                            name="gallery_images[]" 
                            multiple 
                            accept="image/jpeg,image/png,image/webp" 
                            class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-stone-700 hover:file:bg-stone-100 file:border file:border-stone-300"
                        >
                        <span class="text-[11px] text-stone-500 mt-1 block">Bisa pilih <strong>lebih dari 1 foto sekaligus</strong> (tahan tombol Ctrl / Shift di komputer saat memilih file foto) agar menu memiliki fitur slide foto di pop-up buku menu.</span>
                    </div>
                </div>

                <!-- Toggle Options -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <label class="flex items-center gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" x-model="formIsAvailable" class="w-4 h-4 text-brand-600 rounded">
                        <div>
                            <span class="block text-xs font-bold text-stone-800">Status Tersedia</span>
                            <span class="block text-[10px] text-stone-500">Bisa dipesan pelanggan</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 bg-stone-50 rounded-xl border border-stone-200 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" x-model="formIsFeatured" class="w-4 h-4 text-amber-500 rounded">
                        <div>
                            <span class="block text-xs font-bold text-stone-800">Menu Favorit</span>
                            <span class="block text-[10px] text-stone-500">Tampil di beranda utama</span>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                    <button 
                        type="button" 
                        @click="showModal = false" 
                        class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-600 text-xs font-bold hover:bg-stone-50 transition-colors">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        Simpan Menu
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
