<?php
$waNumber = preg_replace('/[^0-9]/', '', $settings['store_whatsapp'] ?? '6281234567890');
$waMessage = urlencode("Halo " . ($settings['store_name'] ?? 'Restoran') . ", saya ingin bertanya seputar menu & reservasi tempat.");
$waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
?>
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-brand-50/70 via-stone-50 to-stone-50 py-12 sm:py-20 border-b border-stone-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-100 text-brand-800 text-xs font-bold tracking-wide uppercase shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-600 animate-ping"></span>
                    <span><?= htmlspecialchars($settings['hero_badge'] ?? 'Tradisi Kuliner Sejak Puluhan Tahun') ?></span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight leading-[1.15]">
                    <?= htmlspecialchars($settings['hero_title_prefix'] ?? 'Nikmati Kelezatan Asli') ?> <span class="text-gradient"><?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan') ?></span>
                </h1>

                <p class="text-base sm:text-lg text-stone-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    <?php if (!empty($settings['store_description'])): ?>
                        <?= nl2br(htmlspecialchars($settings['store_description'])) ?>
                    <?php else: ?>
                        <?= htmlspecialchars($settings['store_tagline'] ?? 'Cita Rasa Masakan Khas Tradisional & Ikan Bakar Sedap') ?>. Diracik menggunakan rempah pilihan nusantara dengan sambal terasi uleg segar setiap hari.
                    <?php endif; ?>
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="/menu" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-7 py-3.5 bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-700 hover:to-amber-700 text-white font-bold rounded-xl shadow-lg shadow-brand-600/30 hover:shadow-xl hover:-translate-y-0.5 transition-all text-base">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Buka Buku Menu Digital</span>
                    </a>
                    <a href="#lokasi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white hover:bg-stone-100 text-stone-700 font-semibold rounded-xl border border-stone-300 shadow-sm transition-all hover:-translate-y-0.5 text-base">
                        <i class="fa-solid fa-map-location-dot text-brand-600"></i>
                        <span>Lokasi & Kontak</span>
                    </a>
                </div>

                <!-- Highlight Badges -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-stone-200/80 max-w-lg mx-auto lg:mx-0">
                    <div class="text-center lg:text-left">
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900">100%</p>
                        <p class="text-xs text-stone-500 font-medium">Bumbu Alami</p>
                    </div>
                    <div class="text-center lg:text-left">
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900">Segar</p>
                        <p class="text-xs text-stone-500 font-medium">Ikan & Daging Fresh</p>
                    </div>
                    <div class="text-center lg:text-left">
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900">Cepat</p>
                        <p class="text-xs text-stone-500 font-medium">Sajian Hangat</p>
                    </div>
                </div>
            </div>

            <!-- Hero Image Banner / Visual Card -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-sm sm:max-w-md lg:max-w-none">
                    <!-- Glow Backdrop -->
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-brand-500 to-amber-500 rounded-3xl blur opacity-25"></div>
                    
                    <div class="relative bg-white p-4 sm:p-5 rounded-2xl shadow-xl border border-stone-100 overflow-hidden">
                        <!-- Culinary Showcase Image / Styled Mockup -->
                        <?php
                            $bannerImg = !empty($settings['store_banner']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_banner'])
                                ? '/assets/uploads/' . htmlspecialchars($settings['store_banner'])
                                : 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80';
                        ?>
                        <div class="relative h-64 sm:h-72 rounded-xl overflow-hidden bg-gradient-to-tr from-stone-900 to-stone-700 flex flex-col justify-end p-5 text-white">
                            <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-60" style="background-image: url('<?= $bannerImg ?>');"></div>
                            
                            <span class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-stone-950 text-xs font-bold shadow-md">
                                <i class="fa-solid fa-star"></i> Menu Legendaris
                            </span>

                            <div class="relative z-10">
                                <h3 class="text-xl font-bold"><?= htmlspecialchars($settings['store_name'] ?? 'Kang Naryo') ?></h3>
                                <p class="text-xs text-stone-300 mt-1"><?= htmlspecialchars($settings['store_tagline'] ?? 'Cita rasa khas nusantara.') ?></p>
                            </div>
                        </div>

                        <!-- Mini Info Bar under photo -->
                        <div class="mt-4 p-3 bg-stone-50 rounded-xl flex items-center justify-between border border-stone-200/60">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-stone-900">Buka Setiap Hari</p>
                                    <p class="text-[11px] text-stone-500"><?= htmlspecialchars($settings['store_hours'] ?? '10.00 - 22.00 WIB') ?></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Buka Sekarang
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Menu Favorit Preview Section -->
<section class="py-12 sm:py-16 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-brand-600 mb-1">
                <i class="fa-solid fa-fire"></i> Rekomendasi Pengunjung
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-stone-900">Menu Andalan Kami</h2>
            <p class="text-stone-600 text-sm mt-1">Pilihan masakan paling favorit yang wajib Anda coba saat berkunjung.</p>
        </div>
        <a href="/menu" class="inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors">
            <span>Lihat Semua <?= count($products) ?> Menu Lengkap</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
            $featuredList = array_filter($products, fn($p) => ($p['is_featured'] ?? 0) == 1);
            if (empty($featuredList)) {
                $featuredList = array_slice($products, 0, 3);
            }
        ?>
        <?php foreach ($featuredList as $item): ?>
            <div class="group bg-white rounded-2xl border border-stone-200/80 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col justify-between hover:border-brand-300">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="inline-block text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-stone-100 text-stone-700">
                            <?= htmlspecialchars($item['category_name'] ?? 'Menu') ?>
                        </span>
                        <?php if ($item['is_available'] == 0): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-100 text-red-700 text-xs font-bold">
                                <i class="fa-solid fa-circle-xmark"></i> Habis
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">
                                <i class="fa-solid fa-thumbs-up text-[10px]"></i> Favorit
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-bold text-stone-900 group-hover:text-brand-600 transition-colors">
                        <?= htmlspecialchars($item['name']) ?>
                    </h3>
                    <p class="text-xs sm:text-sm text-stone-500 mt-2 line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($item['description'] ?? 'Pilihan lezat dengan resep rempah pilihan nusantara.') ?>
                    </p>
                </div>

                <div class="px-5 py-4 bg-stone-50/80 border-t border-stone-100 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-stone-400 block font-medium">Harga</span>
                        <span class="text-base font-extrabold text-stone-900">
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php if ($item['is_available'] == 1): ?>
                        <a href="/menu" class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-colors">
                            Lihat Menu
                        </a>
                    <?php else: ?>
                        <span class="text-xs font-semibold text-stone-400 cursor-not-allowed">
                            Stok Habis
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Lokasi, Jam Buka & Google Maps Section -->
<section id="lokasi" class="py-12 sm:py-16 bg-stone-100/70 border-t border-stone-200 scroll-mt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-brand-600">
                <i class="fa-solid fa-location-dot"></i> Temukan Kami
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-stone-900 mt-1">Lokasi & Jam Operasional</h2>
            <p class="text-stone-600 text-sm mt-1">Kunjungi pondok makan kami dan rasakan suasana makan nyaman bersama keluarga.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Informasi Kontak & Operasional -->
            <div class="lg:col-span-5 space-y-4">
                <!-- Card Alamat -->
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm">Alamat Lengkap</h3>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1 leading-relaxed">
                            <?= htmlspecialchars($settings['store_address'] ?? 'Yogyakarta') ?>
                        </p>
                    </div>
                </div>

                <!-- Card Jam Operasional -->
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm">Jam Operasional</h3>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1">
                            <?= htmlspecialchars($settings['store_hours'] ?? 'Setiap Hari: 10.00 - 22.00 WIB') ?>
                        </p>
                        <p class="text-[11px] text-stone-400 mt-1">Dapur siap melayani makan di tempat (Dine-in) maupun Take Away.</p>
                    </div>
                </div>

                <!-- Card Reservasi WhatsApp -->
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-stone-900 text-sm">Reservasi Meja & Rombongan</h3>
                        <p class="text-xs sm:text-sm text-stone-600 mt-1">Hubungi admin kami langsung via WhatsApp untuk pemesanan rombongan.</p>
                        <a href="<?= $waUrl ?>" target="_blank" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                            <span>Chat WhatsApp Sekarang</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sematan Google Maps -->
            <div class="lg:col-span-7">
                <div class="bg-white p-2.5 rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                    <div class="w-full h-80 sm:h-96 rounded-xl overflow-hidden bg-stone-200 relative">
                        <iframe 
                            src="<?= htmlspecialchars($settings['store_maps_embed'] ?? $settings['store_maps_url'] ?? 'https://maps.google.com/maps?q=Yogyakarta&t=&z=14&ie=UTF8&iwloc=&output=embed') ?>" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta Lokasi Toko">
                        </iframe>
                    </div>
                    <div class="p-3 text-center">
                        <a href="https://maps.google.com/?q=<?= urlencode(($settings['store_name'] ?? 'Resto') . ' ' . ($settings['store_address'] ?? 'Yogyakarta')) ?>" 
                           target="_blank" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:underline">
                            <i class="fa-solid fa-diamond-turn-right"></i> Buka Rute di Google Maps Aplikasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
