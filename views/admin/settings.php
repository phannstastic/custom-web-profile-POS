<!-- Store Settings & White-label Customization Page -->
<div x-data="{
    primaryColor: '<?= htmlspecialchars($settings['theme_primary_color'] ?? '#ea580c') ?>',
    setPresetColor(hex) {
        this.primaryColor = hex;
    }
}" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Identitas Toko & Kustomisasi White-Label</h1>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Ubah nama usaha, logo, banner, kontak, dan palet warna tema untuk klien UMKM.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs sm:text-sm font-bold transition-all">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Lihat Tampilan Web</span>
            </a>
        </div>
    </div>

    <form method="POST" action="/admin/settings/update" enctype="multipart/form-data" class="space-y-6">
        
        <!-- SECTION 1: IDENTITAS UTAMA TOKO -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-stone-100 pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Identitas Utama Usaha / Resto</h2>
                    <p class="text-xs text-stone-500">Nama dan tagline yang muncul pada header situs, judul browser, dan cetakan struk kasir.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Nama Toko / Resto *</label>
                    <input 
                        type="text" 
                        name="store_name" 
                        required 
                        value="<?= htmlspecialchars($settings['store_name'] ?? 'Resto Custom') ?>"
                        placeholder="Contoh: Ayam Geprek Bu Rini / Warung Masakan Enak" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all font-semibold text-stone-900"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Tagline / Slogan Utama</label>
                    <input 
                        type="text" 
                        name="store_tagline" 
                        value="<?= htmlspecialchars($settings['store_tagline'] ?? '') ?>"
                        placeholder="Contoh: Geprek Asli Dari Jogja / Cita Rasa Masakan Khas Tradisional" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all text-stone-900"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Badge / Label Sambutan Depan</label>
                    <input 
                        type="text" 
                        name="hero_badge" 
                        value="<?= htmlspecialchars($settings['hero_badge'] ?? 'Tradisi Kuliner Sejak Puluhan Tahun') ?>"
                        placeholder="Contoh: Tradisi Kuliner Sejak Puluhan Tahun / Resep Asli Khas Jogja" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all text-stone-900"
                    >
                    <span class="text-[11px] text-stone-400 mt-1 block">Teks badge kecil dengan animasi di atas judul web profil.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Awalan Judul Utama (Headline)</label>
                    <input 
                        type="text" 
                        name="hero_title_prefix" 
                        value="<?= htmlspecialchars($settings['hero_title_prefix'] ?? 'Nikmati Kelezatan Asli') ?>"
                        placeholder="Contoh: Nikmati Kelezatan Asli / Selamat Datang di" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all text-stone-900"
                    >
                    <span class="text-[11px] text-stone-400 mt-1 block">Diikuti oleh Nama Toko dengan warna gradasi mencolok.</span>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Narasi / Deskripsi Cerita Usaha & Cita Rasa</label>
                    <textarea 
                        name="store_description" 
                        rows="3" 
                        placeholder="Contoh: Diracik menggunakan rempah pilihan nusantara dengan sambal terasi uleg segar setiap hari. Dibuat dari ayam segar pilihan dengan bumbu meresap..." 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all leading-relaxed text-stone-900"><?= htmlspecialchars($settings['store_description'] ?? '') ?></textarea>
                    <span class="text-[11px] text-stone-400 mt-1 block">Teks panjang yang mendeskripsikan keistimewaan rasa, racikan sambal, dan filosofi masakan Anda di halaman depan.</span>
                </div>
            </div>

            <!-- Upload Logo & Banner -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                <!-- Upload Logo -->
                <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Logo Toko (PNG/SVG Transparan)</label>
                        <?php if (!empty($settings['store_logo']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_logo'])): ?>
                            <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Logo Aktif</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-stone-200 flex items-center justify-center p-1.5 shrink-0 overflow-hidden shadow-xs">
                            <?php if (!empty($settings['store_logo']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_logo'])): ?>
                                <img src="/assets/uploads/<?= htmlspecialchars($settings['store_logo']) ?>" alt="Logo Toko" class="max-h-full max-w-full object-contain">
                            <?php else: ?>
                                <i class="fa-solid fa-utensils text-2xl text-stone-300"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow">
                            <input 
                                type="file" 
                                name="store_logo" 
                                accept="image/png,image/svg+xml,image/jpeg,image/webp" 
                                class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-brand-700 hover:file:bg-brand-50 file:border file:border-stone-200"
                            >
                            <span class="text-[10px] text-stone-400 mt-1 block">Rekomendasi rasio 1:1 format transparan. Tampil di navbar & favicon.</span>
                        </div>
                    </div>
                </div>

                <!-- Upload Banner Hero -->
                <div class="p-4 bg-stone-50 rounded-2xl border border-stone-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-stone-700 uppercase tracking-wider">Foto / Banner Depan Web Profil</label>
                        <?php if (!empty($settings['store_banner']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_banner'])): ?>
                            <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Banner Kustom</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-16 rounded-2xl bg-white border border-stone-200 flex items-center justify-center p-1 shrink-0 overflow-hidden shadow-xs">
                            <?php if (!empty($settings['store_banner']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_banner'])): ?>
                                <img src="/assets/uploads/<?= htmlspecialchars($settings['store_banner']) ?>" alt="Banner Toko" class="w-full h-full object-cover rounded-xl">
                            <?php else: ?>
                                <i class="fa-solid fa-image text-2xl text-stone-300"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow">
                            <input 
                                type="file" 
                                name="store_banner" 
                                accept="image/jpeg,image/png,image/webp" 
                                class="w-full text-xs text-stone-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-brand-700 hover:file:bg-brand-50 file:border file:border-stone-200"
                            >
                            <span class="text-[10px] text-stone-400 mt-1 block">Tampil pada hero section mockup halaman depan.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: PALET WARNA TEMA (WHITE-LABEL THEME) -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-stone-100 pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Kustomisasi Tema Warna Brand (Primary Color)</h2>
                    <p class="text-xs text-stone-500">Sesuaikan warna tombol, badge, dan aksen seluruh web agar sesuai identitas merek klien.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <!-- Color Picker & Live Hex Input -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input 
                            type="color" 
                            name="theme_primary_color" 
                            x-model="primaryColor"
                            class="w-14 h-14 rounded-2xl cursor-pointer border-2 border-white shadow-md p-0 overflow-hidden"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Kode Hex Warna</label>
                        <input 
                            type="text" 
                            x-model="primaryColor" 
                            class="px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-xl text-sm font-mono uppercase font-bold text-stone-800 w-32 focus:outline-none focus:ring-2 focus:ring-brand-500"
                        >
                    </div>
                </div>

                <!-- Preset Quick Buttons -->
                <div>
                    <span class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Preset Warna Pilihan Cepat:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Oranye Tradisional -->
                        <button type="button" @click="setPresetColor('#ea580c')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105" style="background-color: #ea580c;">
                            <span class="w-2 h-2 rounded-full bg-white"></span> Oranye
                        </button>
                        <!-- Merah Selera -->
                        <button type="button" @click="setPresetColor('#dc2626')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105" style="background-color: #dc2626;">
                            <span class="w-2 h-2 rounded-full bg-white"></span> Merah
                        </button>
                        <!-- Hijau Alami -->
                        <button type="button" @click="setPresetColor('#16a34a')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105" style="background-color: #16a34a;">
                            <span class="w-2 h-2 rounded-full bg-white"></span> Hijau
                        </button>
                        <!-- Biru Modern -->
                        <button type="button" @click="setPresetColor('#2563eb')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105" style="background-color: #2563eb;">
                            <span class="w-2 h-2 rounded-full bg-white"></span> Biru
                        </button>
                        <!-- Cokelat Nusantara -->
                        <button type="button" @click="setPresetColor('#78350f')" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105" style="background-color: #78350f;">
                            <span class="w-2 h-2 rounded-full bg-white"></span> Cokelat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview Box -->
            <div class="p-4 rounded-xl border border-stone-200 bg-stone-50 flex items-center justify-between">
                <span class="text-xs text-stone-600 font-semibold">Live Preview Tombol:</span>
                <button type="button" :style="'background-color: ' + primaryColor" class="px-4 py-2 text-white font-bold rounded-xl text-xs shadow-md transition-all">
                    Contoh Tombol Aksi Pesan
                </button>
            </div>
        </div>

        <!-- SECTION 3: KONTAK, LOKASI & MAPS -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-stone-100 pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Kontak, WhatsApp & Lokasi Google Maps</h2>
                    <p class="text-xs text-stone-500">Nomor kontak pemesanan dan koordinat peta restoran untuk pelanggan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">No. WhatsApp Pemilik / Admin *</label>
                    <input 
                        type="text" 
                        name="store_phone" 
                        required 
                        value="<?= htmlspecialchars($settings['store_phone'] ?? $settings['store_whatsapp'] ?? '081234567890') ?>"
                        placeholder="Contoh: 081234567890 / 6281234567890" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all font-mono"
                    >
                    <span class="text-[11px] text-stone-400 mt-1 block">Otomatis dihubungkan ke tombol floating WhatsApp & reservasi.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Jam Operasional</label>
                    <input 
                        type="text" 
                        name="store_hours" 
                        value="<?= htmlspecialchars($settings['store_hours'] ?? 'Setiap Hari: 10.00 - 22.00 WIB') ?>"
                        placeholder="Contoh: Setiap Hari: 10.00 - 22.00 WIB" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Alamat Lengkap Usaha</label>
                    <textarea 
                        name="store_address" 
                        rows="2" 
                        placeholder="Alamat jalan, nomor, kecamatan, kota..." 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all leading-relaxed"><?= htmlspecialchars($settings['store_address'] ?? '') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Link Embed Google Maps (`iframe src`)</label>
                    <input 
                        type="text" 
                        name="store_maps_embed" 
                        value="<?= htmlspecialchars($settings['store_maps_embed'] ?? $settings['store_maps_url'] ?? '') ?>"
                        placeholder="https://maps.google.com/maps?q=...&output=embed" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all font-mono text-xs"
                    >
                    <span class="text-[11px] text-stone-400 mt-1 block">Buka Google Maps > Bagikan > Sematkan peta > Salin URL di atribut src.</span>
                </div>
            </div>
        </div>

        <!-- SECTION 4: KUSTOMISASI STRUK NOTA KASIR -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-stone-100 pb-3 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-stone-900">Pesan Kaki Struk Nota Kasir (Thermal Receipt Footer)</h2>
                    <p class="text-xs text-stone-500">Teks penutup yang otomatis dicetak di bagian paling bawah struk kasir 58mm/80mm.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Pesan Footer Struk</label>
                <input 
                    type="text" 
                    name="receipt_footer" 
                    value="<?= htmlspecialchars($settings['receipt_footer'] ?? 'Terima Kasih Atas Kunjungan Anda! Selamat Menikmati.') ?>"
                    placeholder="Contoh: Terima Kasih Atas Kunjungan Anda! Selamat Menikmati." 
                    class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                >
            </div>
        </div>

        <!-- SECTION 5: INTEGRASI MIDTRANS SNAP QRIS PAYMENT -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-stone-900">Integrasi Pembayaran QRIS Otomatis (Midtrans Snap)</h2>
                        <p class="text-xs text-stone-500">Menerima pembayaran QRIS langsung di kasir dengan auto-verifikasi lunas.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold <?= !empty($settings['midtrans_server_key']) ? 'text-emerald-600 bg-emerald-50' : 'text-stone-400 bg-stone-100' ?> px-3 py-1 rounded-full border">
                        <?= !empty($settings['midtrans_server_key']) ? '● Terkonfigurasi' : '○ Belum Aktif' ?>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Server Key *</label>
                    <input 
                        type="text" 
                        name="midtrans_server_key" 
                        value="<?= htmlspecialchars($settings['midtrans_server_key'] ?? '') ?>"
                        placeholder="SB-Mid-server-xxxx / Mid-server-xxxx" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                    >
                    <span class="text-[10px] text-stone-400 mt-1 block">Dapat dilihat di Dashboard Midtrans > Settings > Access Keys.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Client Key *</label>
                    <input 
                        type="text" 
                        name="midtrans_client_key" 
                        value="<?= htmlspecialchars($settings['midtrans_client_key'] ?? '') ?>"
                        placeholder="SB-Mid-client-xxxx / Mid-client-xxxx" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all"
                    >
                    <span class="text-[10px] text-stone-400 mt-1 block">Digunakan oleh frontend Snap JS kasir.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Mode Lingkungan (Environment)</label>
                    <select 
                        name="midtrans_is_production" 
                        class="w-full px-4 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                        <option value="0" <?= ($settings['midtrans_is_production'] ?? '0') === '0' ? 'selected' : '' ?>>
                            Sandbox (Simulasi / Uji Coba)
                        </option>
                        <option value="1" <?= ($settings['midtrans_is_production'] ?? '') === '1' ? 'selected' : '' ?>>
                            Production (Transaksi Riil QRIS)
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Webhook Notification URL</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            readonly 
                            value="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8008') . '/api/payment/webhook' ?>" 
                            class="w-full px-3 py-2 bg-stone-100 border border-stone-200 rounded-xl text-xs font-mono text-stone-600 select-all"
                        >
                    </div>
                    <span class="text-[10px] text-stone-400 mt-1 block">Salin URL ini ke Dashboard Midtrans > Settings > Configuration > Payment Notification URL.</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <button 
                type="submit" 
                class="px-8 py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-extrabold rounded-xl shadow-lg shadow-brand-600/30 hover:shadow-xl transition-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Pengaturan Identitas Toko & Midtrans</span>
            </button>
        </div>

    </form>

</div>
