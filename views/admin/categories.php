<!-- Category Management Page -->
<div x-data="{
    showModal: false,
    isEdit: false,
    formId: '',
    formName: '',
    formIcon: 'fa-utensils',

    openAddModal() {
        this.isEdit = false;
        this.formId = '';
        this.formName = '';
        this.formIcon = 'fa-utensils';
        this.showModal = true;
    },

    openEditModal(cat) {
        this.isEdit = true;
        this.formId = cat.id;
        this.formName = cat.name;
        this.formIcon = cat.icon || 'fa-utensils';
        this.showModal = true;
    }
}" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Kategori Menu</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Kelompokkan menu makanan, minuman, dan camilan resto.</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-brand-600/20 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Category Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-stone-600">
                <thead class="bg-stone-50 text-stone-700 text-xs uppercase tracking-wider font-bold border-b border-stone-200">
                    <tr>
                        <th class="px-6 py-4">Ikon</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Slug URL</th>
                        <th class="px-6 py-4">Jumlah Produk</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-stone-400 text-sm">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-stone-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base border border-brand-100">
                                        <i class="fa-solid <?= htmlspecialchars($cat['icon'] ?: 'fa-utensils') ?>"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-stone-900">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-stone-500">
                                    <?= htmlspecialchars($cat['slug']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-stone-100 text-stone-700 text-xs font-semibold">
                                        <i class="fa-solid fa-bowl-food text-[10px] text-stone-400"></i>
                                        <?= (int) ($cat['product_count'] ?? 0) ?> Menu
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button 
                                            @click="openEditModal(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="p-2 text-stone-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        
                                        <form method="POST" action="/admin/categories/delete" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Menu terkait tidak akan terhapus.');">
                                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

    <!-- Modal Form (Create / Edit) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div 
            @click.away="showModal = false" 
            class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-stone-100 space-y-5">
            
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-lg font-bold text-stone-900" x-text="isEdit ? 'Edit Kategori Menu' : 'Tambah Kategori Baru'"></h3>
                <button @click="showModal = false" class="text-stone-400 hover:text-stone-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" :action="isEdit ? '/admin/categories/update' : '/admin/categories/create'" class="space-y-4">
                <input type="hidden" name="id" :value="formId" x-show="isEdit">

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Kategori</label>
                    <input 
                        type="text" 
                        name="name" 
                        x-model="formName" 
                        required 
                        placeholder="Contoh: Paket Hemat" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Ikon Font Awesome</label>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-700 shrink-0">
                            <i :class="'fa-solid ' + (formIcon || 'fa-utensils')"></i>
                        </div>
                        <input 
                            type="text" 
                            name="icon" 
                            x-model="formIcon" 
                            placeholder="fa-utensils / fa-glass-water / fa-bowl-food" 
                            class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all font-mono"
                        >
                    </div>
                    <span class="text-[11px] text-stone-400 mt-1 block">Rekomendasi: fa-utensils, fa-glass-water, fa-bowl-food, fa-mug-hot, fa-fire</span>
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
                        Simpan Kategori
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
