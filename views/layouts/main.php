<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Pondok Makan Kang Naryo') ?></title>
    <?php
        $primaryColor = !empty($settings['theme_primary_color']) ? htmlspecialchars($settings['theme_primary_color']) : '#ea580c';
        $storeLogo = !empty($settings['store_logo']) && file_exists(dirname(__DIR__, 2) . '/public/assets/uploads/' . $settings['store_logo']) 
                     ? '/assets/uploads/' . htmlspecialchars($settings['store_logo']) 
                     : null;
    ?>
    <?php if ($storeLogo): ?>
        <link rel="icon" type="image/png" href="<?= $storeLogo ?>">
        <link rel="apple-touch-icon" href="<?= $storeLogo ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="/assets/images/icon-192.png">
        <link rel="apple-touch-icon" href="/assets/images/icon-192.png">
    <?php endif; ?>

    <!-- PWA Web App Manifest & Mobile Metas -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="<?= $primaryColor ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="POS Kang Naryo">

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
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: <?= $primaryColor ?>;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .text-gradient {
            background: linear-gradient(135deg, <?= $primaryColor ?>, color-mix(in srgb, <?= $primaryColor ?> 80%, black));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-theme-primary {
            background-color: <?= $primaryColor ?> !important;
        }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased min-h-screen flex flex-col selection:bg-brand-500 selection:text-white">

    <!-- Navbar Header -->
    <header class="glass-nav sticky top-0 z-40 border-b border-stone-200 shadow-sm transition-all duration-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <?php if ($storeLogo): ?>
                    <img src="<?= $storeLogo ?>" alt="Logo Toko" class="w-10 h-10 rounded-xl object-contain shadow-sm group-hover:scale-105 transition-transform">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-amber-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-utensils text-lg"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-stone-900 group-hover:text-brand-600 transition-colors leading-tight">
                        <?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?>
                    </h1>
                    <p class="text-xs text-stone-500 hidden sm:block"><?= htmlspecialchars($settings['store_tagline'] ?? 'Kuliner Tradisional & Ikan Bakar') ?></p>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="/" class="px-3 py-2 rounded-lg text-sm font-semibold <?= ($activeNav ?? '') === 'home' ? 'text-brand-600 bg-brand-50' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' ?> transition-colors">
                    Beranda
                </a>
                <a href="/menu" class="px-3 py-2 rounded-lg text-sm font-semibold <?= ($activeNav ?? '') === 'menu' ? 'text-brand-600 bg-brand-50' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' ?> transition-colors">
                    Buku Menu Digital
                </a>
                <a href="/#lokasi" class="px-3 py-2 rounded-lg text-sm font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition-colors">
                    Lokasi & Jam Buka
                </a>
                <a href="/pos" class="ml-2 px-3.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider text-amber-900 bg-amber-100 hover:bg-amber-200 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register"></i> Kasir POS
                </a>
            </nav>

            <!-- Action CTA -->
            <div class="flex items-center gap-2">
                <a href="/menu" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-brand-600 to-amber-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-brand-600/20 hover:from-brand-700 hover:to-amber-700 transition-all hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Lihat Menu</span>
                </a>

                <!-- Mobile Navigation Trigger (handled simply with icons) -->
                <a href="/menu" class="sm:hidden p-2 rounded-lg text-brand-600 bg-brand-50 hover:bg-brand-100 transition-colors" title="Buka Menu">
                    <i class="fa-solid fa-book-open text-lg"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- WhatsApp Floating Action Button -->
    <?php
        $waNumber = preg_replace('/[^0-9]/', '', $settings['store_whatsapp'] ?? '6281234567890');
        $waMessage = urlencode("Halo " . ($settings['store_name'] ?? 'Pondok Makan Kang Naryo') . ", saya ingin bertanya seputar menu & reservasi tempat.");
        $waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
    ?>
    <aside aria-label="Bantuan WhatsApp" class="fixed bottom-6 right-6 z-50">
        <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" 
           class="group flex items-center gap-3 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-full shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-1 transition-all duration-300">
            <i class="fa-brands fa-whatsapp text-2xl animate-pulse"></i>
            <span class="font-bold text-sm hidden sm:inline-block pr-1 group-hover:underline">Reservasi / Tanya Admin</span>
        </a>
    </aside>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-300 border-t border-stone-800 mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Info Toko -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white text-sm">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <h2 class="text-white font-bold text-lg tracking-tight"><?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?></h2>
                    </div>
                    <p class="text-sm text-stone-400 leading-relaxed">
                        <?= htmlspecialchars($settings['store_tagline'] ?? 'Cita Rasa Masakan Khas Tradisional & Ikan Bakar Sedap') ?>
                    </p>
                    <p class="text-xs text-brand-400 font-medium">
                        <i class="fa-solid fa-heart mr-1 text-red-500"></i> Melayani dengan ketulusan cita rasa warisan nusantara.
                    </p>
                </div>

                <!-- Jam Operasional & Kontak -->
                <div class="space-y-3">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider">Jam Buka & Kontak</h3>
                    <ul class="space-y-2 text-sm text-stone-400">
                        <li class="flex items-start gap-2">
                            <i class="fa-regular fa-clock text-brand-500 mt-1"></i>
                            <span><?= htmlspecialchars($settings['store_hours'] ?? 'Setiap Hari: 10.00 - 22.00 WIB') ?></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-phone text-brand-500 mt-1"></i>
                            <span><?= htmlspecialchars($settings['store_phone'] ?? '081234567890') ?></span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-brand-500 mt-1"></i>
                            <span><?= htmlspecialchars($settings['store_address'] ?? 'Yogyakarta') ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Tautan Pintas -->
                <div class="space-y-3">
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider">Tautan Pintas</h3>
                    <div class="flex flex-col gap-2 text-sm text-stone-400">
                        <a href="/" class="hover:text-brand-400 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Beranda Utama
                        </a>
                        <a href="/menu" class="hover:text-brand-400 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Katalog Menu Digital
                        </a>
                        <a href="/pos" class="hover:text-brand-400 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Sistem Kasir POS
                        </a>
                        <a href="/admin" class="hover:text-brand-400 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-angle-right text-xs"></i> Dashboard Admin
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500 gap-4">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?>. All rights reserved.</p>
                <p><?= htmlspecialchars($settings['receipt_footer'] ?? '') ?></p>
            </div>
        </div>
    </footer>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('[PWA] Service Worker terdaftar:', reg.scope))
                    .catch((err) => console.warn('[PWA] Service Worker gagal daftar:', err));
            });
        }
    </script>
</body>
</html>
