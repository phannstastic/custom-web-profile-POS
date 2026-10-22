<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Backoffice Admin - Panel Kelola</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-stone-900 text-stone-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-stone-800 border border-stone-700/80 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Glow top -->
        <div class="absolute -top-16 -left-16 w-32 h-32 bg-brand-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-amber-500 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl shadow-lg shadow-brand-500/20 mb-3">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Login Backoffice Admin</h1>
            <p class="text-xs text-stone-400 mt-1"><?= htmlspecialchars($settings['store_name'] ?? 'Resto & POS System') ?></p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 bg-red-500/20 border border-red-500/40 text-red-300 text-xs px-4 py-3 rounded-xl flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-base shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/login" class="space-y-4">
            <div>
                <label for="username" class="block text-xs font-semibold text-stone-300 mb-1.5 uppercase tracking-wider">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-500 pointer-events-none">
                        <i class="fa-solid fa-user text-xs"></i>
                    </span>
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        required 
                        placeholder="Contoh: admin" 
                        class="w-full bg-stone-900 border border-stone-700 rounded-xl pl-9 pr-4 py-2.5 text-sm text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                        value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>"
                    >
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-stone-300 mb-1.5 uppercase tracking-wider">Password / PIN</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-500 pointer-events-none">
                        <i class="fa-solid fa-key text-xs"></i>
                    </span>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        required 
                        placeholder="Masukkan password admin" 
                        class="w-full bg-stone-900 border border-stone-700 rounded-xl pl-9 pr-4 py-2.5 text-sm text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all"
                    >
                </div>
                <div class="mt-2 text-right">
                    <span class="text-[11px] text-stone-500">Default: <code>admin</code> / <code>admin123</code></span>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full py-3 px-4 bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-700 hover:to-amber-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-brand-600/30 hover:shadow-xl transition-all">
                Masuk ke Backoffice
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-stone-700/60 text-center">
            <a href="/" class="text-xs text-stone-400 hover:text-white transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Beranda Publik</span>
            </a>
        </div>
    </div>

</body>
</html>
