<!-- Laporan Penjualan & Riwayat Transaksi -->
<div class="space-y-6" x-data="reportsModule()">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-stone-500 font-semibold uppercase tracking-wider mb-1">
                <span>Manajemen Transaksi</span>
                <span>&bull;</span>
                <span class="text-brand-600">Issue #7 Selesai</span>
            </div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Laporan Rekapitulasi & Penjualan</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                Pantau perolehan omset harian, proporsi pembayaran tunai vs QRIS, serta riwayat transaksi pesanan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Export CSV -->
            <a :href="'/admin/reports/export?' + currentQueryParams()" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Export CSV / Excel</span>
            </a>

            <!-- Tombol Cetak / Print Halaman -->
            <button type="button" @click="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-stone-800 hover:bg-stone-900 text-white rounded-xl text-xs sm:text-sm font-bold transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fa-solid fa-print text-sm"></i>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
        <form method="GET" action="/admin/reports" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">Dari Tanggal</label>
                <div class="relative">
                    <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>" 
                           class="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-stone-300 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-stone-800 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">Sampai Tanggal</label>
                <div class="relative">
                    <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>" 
                           class="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-stone-300 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-stone-800 transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">Metode Bayar</label>
                <select name="payment_method" class="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-stone-300 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-stone-800 transition-all">
                    <option value="all" <?= ($filters['payment_method'] === 'all') ? 'selected' : '' ?>>Semua Metode</option>
                    <option value="cash" <?= ($filters['payment_method'] === 'cash') ? 'selected' : '' ?>>Tunai (Cash)</option>
                    <option value="qris" <?= ($filters['payment_method'] === 'qris') ? 'selected' : '' ?>>Non-Tunai (QRIS)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1.5">Status Transaksi</label>
                <select name="payment_status" class="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-stone-300 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-stone-800 transition-all">
                    <option value="all" <?= ($filters['payment_status'] === 'all') ? 'selected' : '' ?>>Semua Status</option>
                    <option value="paid" <?= ($filters['payment_status'] === 'paid') ? 'selected' : '' ?>>Lunas (Paid)</option>
                    <option value="pending" <?= ($filters['payment_status'] === 'pending') ? 'selected' : '' ?>>Menunggu (Pending)</option>
                    <option value="cancelled" <?= ($filters['payment_status'] === 'cancelled') ? 'selected' : '' ?>>Batal (Cancelled)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-600/20 transition-all">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan</span>
                </button>
                <a href="/admin/reports" title="Reset Hari Ini" class="inline-flex items-center justify-center p-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl text-xs font-bold transition-all">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Quick filter presets -->
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-stone-100 text-xs">
            <span class="text-stone-400 font-medium">Preset cepat:</span>
            <a href="/admin/reports?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" 
               class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold transition-all">
               Hari Ini
            </a>
            <a href="/admin/reports?start_date=<?= date('Y-m-d', strtotime('-6 days')) ?>&end_date=<?= date('Y-m-d') ?>" 
               class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold transition-all">
               7 Hari Terakhir
            </a>
            <a href="/admin/reports?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" 
               class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold transition-all">
               Bulan Ini
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Omset -->
        <div class="bg-gradient-to-br from-brand-600 to-amber-600 text-white p-5 rounded-2xl shadow-lg shadow-brand-600/20 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <span class="text-xs uppercase tracking-wider font-bold text-amber-100 block">Total Omset (Lunas)</span>
                <p class="text-2xl font-black mt-1">Rp <?= number_format($summary['total_revenue'], 0, ',', '.') ?></p>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] font-medium text-amber-100">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= $summary['total_paid_transactions'] ?> transaksi terverifikasi</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        </div>

        <!-- Penerimaan Tunai -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider block">Penerimaan Tunai</span>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">
                    Rp <?= number_format($summary['total_cash_revenue'], 0, ',', '.') ?>
                </p>
                <span class="text-[11px] text-stone-500 font-semibold mt-1 inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                    <?= $summary['total_revenue'] > 0 ? round(($summary['total_cash_revenue'] / $summary['total_revenue']) * 100, 1) : 0 ?>% dari total omset
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Penerimaan QRIS -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider block">Penerimaan QRIS</span>
                <p class="text-2xl font-extrabold text-blue-600 mt-1">
                    Rp <?= number_format($summary['total_qris_revenue'], 0, ',', '.') ?>
                </p>
                <span class="text-[11px] text-stone-500 font-semibold mt-1 inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                    <?= $summary['total_revenue'] > 0 ? round(($summary['total_qris_revenue'] / $summary['total_revenue']) * 100, 1) : 0 ?>% dari total omset
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-qrcode"></i>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-stone-500 font-bold uppercase tracking-wider block">Jumlah Transaksi</span>
                <p class="text-2xl font-extrabold text-stone-900 mt-1">
                    <?= $summary['total_transactions'] ?>
                </p>
                <div class="flex items-center gap-2 mt-1 text-[11px] font-semibold">
                    <span class="text-emerald-600"><?= $summary['total_paid_transactions'] ?> Lunas</span>
                    <?php if ($summary['total_pending_transactions'] > 0): ?>
                        <span class="text-amber-500">&bull; <?= $summary['total_pending_transactions'] ?> Menunggu</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-stone-100 text-stone-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

    </div>

    <!-- Transaction Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-sm overflow-hidden">
        
        <!-- Table Header & Search -->
        <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-stone-900">Riwayat Transaksi Pesanan</h2>
                <p class="text-xs text-stone-500 mt-0.5">
                    Menampilkan total <?= count($orders) ?> catatan transaksi sesuai kriteria filter.
                </p>
            </div>

            <!-- Client-side / server-side search input -->
            <form method="GET" action="/admin/reports" class="relative">
                <input type="hidden" name="start_date" value="<?= htmlspecialchars($filters['start_date']) ?>">
                <input type="hidden" name="end_date" value="<?= htmlspecialchars($filters['end_date']) ?>">
                <input type="hidden" name="payment_method" value="<?= htmlspecialchars($filters['payment_method']) ?>">
                <input type="hidden" name="payment_status" value="<?= htmlspecialchars($filters['payment_status']) ?>">

                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" 
                           placeholder="Cari nota / nama meja..." 
                           class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 font-semibold transition-all">
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-stone-50/80 text-stone-600 uppercase tracking-wider text-[11px] font-bold border-b border-stone-100">
                        <th class="py-3 px-4">No. Nota</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pelanggan & Meja</th>
                        <th class="py-3 px-4">Ringkasan Item</th>
                        <th class="py-3 px-4">Total Belanja</th>
                        <th class="py-3 px-4 text-center">Metode</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-medium text-stone-700">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-stone-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-stone-300"></i>
                                <span class="font-bold text-sm block text-stone-600">Tidak ada transaksi ditemukan</span>
                                <span class="text-xs">Coba ubah rentang tanggal atau bersihkan filter pencarian.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <!-- Invoice -->
                                <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-receipt text-stone-400 text-xs"></i>
                                        <span><?= htmlspecialchars($order['invoice_number']) ?></span>
                                    </div>
                                </td>

                                <!-- Waktu -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-stone-500">
                                    <div class="font-semibold text-stone-800"><?= date('H:i', strtotime($order['created_at'])) ?> WIB</div>
                                    <div class="text-[10px] text-stone-400"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                </td>

                                <!-- Pelanggan & Meja -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-stone-900"><?= htmlspecialchars($order['customer_name'] ?? 'Pelanggan') ?></div>
                                    <div class="text-[10px] text-brand-700 font-semibold">
                                        <i class="fa-solid fa-chair text-[9px] mr-0.5"></i>
                                        <?= htmlspecialchars($order['table_number'] ?? 'Tanpa Meja') ?>
                                    </div>
                                </td>

                                <!-- Ringkasan Item -->
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="truncate text-stone-600" title="<?= htmlspecialchars($order['items_summary'] ?? '-') ?>">
                                        <?= htmlspecialchars($order['items_summary'] ?? '-') ?>
                                    </div>
                                    <span class="text-[10px] text-stone-400 font-normal">
                                        Total <?= (int) ($order['total_items_count'] ?? 1) ?> jenis menu
                                    </span>
                                </td>

                                <!-- Total -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-bold text-stone-900">
                                    Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
                                </td>

                                <!-- Metode -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <?php if ($order['payment_method'] === 'cash'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                            <i class="fa-solid fa-money-bill-wave text-[9px]"></i> Tunai
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                            <i class="fa-solid fa-qrcode text-[9px]"></i> QRIS
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <?php if ($order['payment_status'] === 'paid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px]"></i> Lunas
                                        </span>
                                    <?php elseif ($order['payment_status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[9px]"></i> Menunggu
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-stone-100 text-stone-500 border border-stone-200">
                                            Batal
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Detail Modal -->
                                        <button type="button" 
                                                @click="viewOrderDetail(<?= (int)$order['id'] ?>)" 
                                                class="px-2.5 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-[11px] transition-all inline-flex items-center gap-1"
                                                title="Lihat Detail Pesanan">
                                            <i class="fa-solid fa-eye text-stone-500"></i>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Tombol Cetak Ulang Nota -->
                                        <a href="/pos/receipt/<?= (int)$order['id'] ?>?autoprint=1" 
                                           target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-[11px] transition-all inline-flex items-center gap-1 border border-brand-200"
                                           title="Cetak Ulang Nota Kasir Thermal">
                                            <i class="fa-solid fa-print"></i>
                                            <span>Cetak Nota</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination / Info -->
        <div class="p-4 bg-stone-50/60 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500 font-medium">
            <span>Rentang Data: <?= date('d M Y', strtotime($filters['start_date'])) ?> s/d <?= date('d M Y', strtotime($filters['end_date'])) ?></span>
            <span class="text-stone-400">Pondok Makan Kang Naryo POS System</span>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div x-cloak x-show="detailModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="detailModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity" 
                 @click="detailModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="detailModalOpen" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-stone-200">
                
                <!-- Modal Header -->
                <div class="p-5 bg-stone-50 border-b border-stone-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Rincian Transaksi</span>
                        <h3 class="text-base font-bold text-stone-900 font-mono" x-text="activeOrder?.invoice_number || 'Loading...'"></h3>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-lg bg-stone-200/60 text-stone-500 hover:text-stone-800 flex items-center justify-center transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-4 text-xs">
                    <div x-show="isLoadingDetail" class="py-8 text-center text-stone-400">
                        <i class="fa-solid fa-circle-notch fa-spin text-2xl text-brand-600 mb-2"></i>
                        <p class="font-semibold">Mengambil rincian data transaksi...</p>
                    </div>

                    <div x-show="!isLoadingDetail && activeOrder">
                        <!-- Info Pelanggan -->
                        <div class="bg-stone-50 p-3.5 rounded-xl border border-stone-200/70 grid grid-cols-2 gap-2 text-stone-600">
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase font-bold block">Pelanggan</span>
                                <span class="font-bold text-stone-800" x-text="activeOrder?.customer_name || 'Pelanggan'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase font-bold block">No. Meja</span>
                                <span class="font-bold text-stone-800" x-text="activeOrder?.table_number || '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase font-bold block">Metode Pembayaran</span>
                                <span class="font-bold uppercase text-stone-800" x-text="activeOrder?.payment_method"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-stone-400 uppercase font-bold block">Waktu Transaksi</span>
                                <span class="font-semibold text-stone-800" x-text="activeOrder?.created_at"></span>
                            </div>
                        </div>

                        <!-- Item list -->
                        <div class="mt-4">
                            <span class="font-bold text-stone-800 block mb-2">Menu Dipesan:</span>
                            <div class="divide-y divide-stone-100 max-h-56 overflow-y-auto border border-stone-200 rounded-xl">
                                <template x-for="item in activeOrder?.items || []" :key="item.id">
                                    <div class="p-2.5 flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-stone-900" x-text="item.product_name"></div>
                                            <div class="text-[11px] text-stone-400">
                                                <span x-text="item.quantity + 'x'"></span> @ Rp <span x-text="formatNumber(item.price)"></span>
                                                <span x-show="item.notes" class="text-amber-600 ml-1 italic" x-text="'(' + item.notes + ')'"></span>
                                            </div>
                                        </div>
                                        <span class="font-bold text-stone-800" x-text="'Rp ' + formatNumber(item.subtotal)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Ringkasan Finansial -->
                        <div class="mt-4 pt-3 border-t border-stone-200 space-y-1.5 font-medium text-stone-600">
                            <div class="flex justify-between">
                                <span>Total Tagihan</span>
                                <span class="font-bold text-stone-900" x-text="'Rp ' + formatNumber(activeOrder?.total_amount || 0)"></span>
                            </div>
                            <div class="flex justify-between" x-show="activeOrder?.payment_method === 'cash'">
                                <span>Uang Tunai Diterima</span>
                                <span x-text="'Rp ' + formatNumber(activeOrder?.cash_paid || 0)"></span>
                            </div>
                            <div class="flex justify-between" x-show="activeOrder?.payment_method === 'cash'">
                                <span>Kembalian</span>
                                <span class="font-bold text-emerald-600" x-text="'Rp ' + formatNumber(activeOrder?.cash_change || 0)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-stone-50 border-t border-stone-200/80 flex items-center justify-end gap-2">
                    <button type="button" @click="detailModalOpen = false" 
                            class="px-4 py-2 bg-stone-200 hover:bg-stone-300 text-stone-700 font-bold rounded-xl text-xs transition-all">
                        Tutup
                    </button>
                    <a :href="'/pos/receipt/' + (activeOrder?.id || '') + '?autoprint=1'" 
                       target="_blank"
                       class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-xs transition-all inline-flex items-center gap-1.5 shadow-md shadow-brand-600/20">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Nota</span>
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
function reportsModule() {
    return {
        detailModalOpen: false,
        isLoadingDetail: false,
        activeOrder: null,

        currentQueryParams() {
            const params = new URLSearchParams(window.location.search);
            return params.toString();
        },

        formatNumber(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },

        async viewOrderDetail(orderId) {
            this.detailModalOpen = true;
            this.isLoadingDetail = true;
            this.activeOrder = null;

            try {
                const res = await fetch(`/api/orders/detail/${orderId}`);
                const data = await res.json();
                if (data.success) {
                    this.activeOrder = data.order;
                } else {
                    alert(data.error || 'Gagal memuat detail transaksi');
                    this.detailModalOpen = false;
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan jaringan.');
                this.detailModalOpen = false;
            } finally {
                this.isLoadingDetail = false;
            }
        }
    }
}
</script>
