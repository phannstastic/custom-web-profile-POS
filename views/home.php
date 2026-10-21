<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Pondok Makan Kang Naryo') ?></title>
    <style>
        :root {
            --primary: #c2410c;
            --primary-hover: #9a3412;
            --dark: #1e293b;
            --light: #f8fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            padding-bottom: 50px;
        }
        header {
            background: linear-gradient(135deg, #ea580c, #c2410c);
            color: #fff;
            padding: 2.5rem 1rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        header p {
            font-size: 1.1rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .status-badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        .nav-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            background: #fff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-links a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        .card {
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        .card .badge {
            display: inline-block;
            background: #fed7aa;
            color: #9a3412;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .card p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .card .price {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
        }
        footer {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?></h1>
        <p><?= htmlspecialchars($settings['store_tagline'] ?? 'Cita Rasa Tradisional Nusantara') ?></p>
        <span class="status-badge">⚡ Core Architecture Ready (Issue #1 Done)</span>
    </header>

    <div class="container">
        <div class="nav-links">
            <a href="/">🏠 Home</a>
            <a href="/menu">📋 Katalog Menu</a>
            <a href="/pos">💻 POS Kasir</a>
            <a href="/admin">⚙️ Admin</a>
            <a href="/api/health" target="_blank">🩺 API Health</a>
            <a href="/api/products" target="_blank">📦 API Products</a>
        </div>

        <h2 style="margin-top: 2rem;">Contoh Menu (Database SQLite Berjalan)</h2>
        <div class="grid">
            <?php foreach ($products as $item): ?>
                <div class="card">
                    <div>
                        <span class="badge"><?= htmlspecialchars($item['category_name'] ?? 'Menu') ?></span>
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= htmlspecialchars($item['description'] ?? '') ?></p>
                    </div>
                    <div class="price">Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer>
            <p><?= htmlspecialchars($settings['store_address'] ?? '') ?> • Telp: <?= htmlspecialchars($settings['store_phone'] ?? '') ?></p>
            <p style="margin-top: 0.5rem; font-style: italic;"><?= htmlspecialchars($settings['receipt_footer'] ?? '') ?></p>
        </footer>
    </div>
</body>
</html>
