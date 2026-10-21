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
        
        <!-- Omset Hari Ini -->
        <div class="bg-gradient-to-br from-brand-600 to-amber-600 text-white p-5 rounded-2xl shadow-md shadow-brand-600/20 flex items-center justify-between">
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-amber-100 block">Omset Hari Ini</span>
                <p class="text-2xl font-black mt-1">Rp <?= number_format($todaySummary['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                <span class="text-[11px] text-amber-100 font-semibold mt-1 inline-block">
                    <?= $todaySummary['total_paid_transactions'] ?? 0 ?> transaksi lunas
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <!-- Total Menu -->
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

        <!-- Menu Habis -->
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

        <!-- Kategori -->
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
    </div>

    <!-- Quick Sales & Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Transaksi Terkini -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Transaksi Kasir Terakhir</h2>
                    <p class="text-xs text-stone-500 mt-0.5">5 transaksi pesanan terbaru</p>
                </div>
                <a href="/admin/reports" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <span>Lihat Semua Laporan</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-stone-50/70 text-stone-500 font-bold uppercase text-[10px] border-b border-stone-100">
                            <th class="py-2.5 px-4">Nota</th>
                            <th class="py-2.5 px-4">Waktu</th>
                            <th class="py-2.5 px-4">Pelanggan</th>
                            <th class="py-2.5 px-4">Total</th>
                            <th class="py-2.5 px-4 text-center">Metode</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                            <th class="py-2.5 px-4 text-right">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="7" class="py-6 text-center text-stone-400 text-xs">Belum ada transaksi</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $ro): ?>
                                <tr class="hover:bg-stone-50/60">
                                    <td class="py-2.5 px-4 font-mono font-bold text-stone-900"><?= htmlspecialchars($ro['invoice_number']) ?></td>
                                    <td class="py-2.5 px-4 text-stone-500"><?= date('H:i', strtotime($ro['created_at'])) ?></td>
                                    <td class="py-2.5 px-4 font-semibold text-stone-800"><?= htmlspecialchars($ro['customer_name'] ?? 'Pelanggan') ?></td>
                                    <td class="py-2.5 px-4 font-bold text-stone-900">Rp <?= number_format($ro['total_amount'], 0, ',', '.') ?></td>
                                    <td class="py-2.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $ro['payment_method'] === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' ?>">
                                            <?= $ro['payment_method'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $ro['payment_status'] === 'paid' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' ?>">
                                            <?= $ro['payment_status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right">
                                        <a href="/pos/receipt/<?= $ro['id'] ?>?autoprint=1" target="_blank" class="p-1.5 rounded-lg text-stone-500 hover:text-brand-600 hover:bg-stone-100 transition-all inline-block">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-stone-50/60 border-t border-stone-100 text-right">
                <a href="/pos" class="text-xs font-bold text-brand-600 hover:underline inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Buka POS Kasir Untuk Transaksi Baru</span>
                </a>
            </div>
        </div>

        <!-- Banner Laporan Ringkas -->
        <div class="bg-gradient-to-br from-stone-900 to-stone-800 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Modul Laporan & Rekap</span>
                <h2 class="text-lg font-bold mt-2">Laporan Penjualan Harian & Transaksi</h2>
                <p class="text-xs text-stone-300 mt-2 leading-relaxed">
                    Lihat detail perputaran uang tunai vs non-tunai (QRIS), filter riwayat transaksi berdasarkan tanggal, dan cetak ulang nota pelanggan kapan saja.
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-stone-700/60 space-y-2">
                <a href="/admin/reports" class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-all shadow-md text-center block">
                    Buka Laporan Lengkap & Export
                </a>
                <a href="/admin/products" class="w-full py-2 px-4 bg-stone-700/50 hover:bg-stone-700 text-stone-300 text-xs font-semibold rounded-xl transition-all text-center block">
                    Kelola Stok & Menu
                </a>
            </div>
        </div>

    </div>
</div>
