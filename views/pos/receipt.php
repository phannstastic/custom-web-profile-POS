<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - <?= htmlspecialchars($order['invoice_number']) ?></title>
    <style>
        /* Standar Kertas Thermal Roll 58mm / 80mm */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        @media print {
            html, body {
                width: 58mm;
                margin: 0;
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .receipt {
                box-shadow: none !important;
                border: none !important;
                padding: 3mm 2mm !important;
                width: 100% !important;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Courier New", Courier, monospace, monospace;
            font-size: 11px;
            line-height: 1.25;
            color: #000;
            background: #f5f5f5;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .receipt {
            background: #fff;
            width: 58mm;
            padding: 8px 6px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .divider-double {
            border-top: 1px double #000;
            margin: 5px 0;
        }

        .store-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .item-name {
            font-weight: bold;
            word-break: break-word;
        }

        .item-note {
            font-size: 10px;
            padding-left: 8px;
            font-style: italic;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .print-toolbar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary { background: #ea580c; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>

    <!-- Tombol Action untuk Browser Preview -->
    <div class="print-toolbar no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Struk (Ctrl+P)
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Tutup
        </button>
    </div>

    <!-- Container Struk Thermal 58mm -->
    <div class="receipt">
        
        <!-- Header -->
        <div class="text-center">
            <div class="store-title uppercase"><?= htmlspecialchars($settings['store_name'] ?? 'Pondok Makan Kang Naryo') ?></div>
            <div><?= htmlspecialchars($settings['store_address'] ?? 'Jl. Kuliner No. 88, Yogyakarta') ?></div>
            <div>Telp: <?= htmlspecialchars($settings['store_phone'] ?? '081234567890') ?></div>
        </div>

        <div class="divider"></div>

        <!-- Info Transaksi -->
        <div style="font-size: 10px;">
            <div class="summary-row">
                <span>No: <?= htmlspecialchars($order['invoice_number']) ?></span>
                <span><?= htmlspecialchars($order['table_number'] ?? 'Meja') ?></span>
            </div>
            <div class="summary-row">
                <span>Tgl: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                <span>Kasir: <?= htmlspecialchars($cashierName ?? 'Kasir') ?></span>
            </div>
            <div class="summary-row">
                <span>Plg: <?= htmlspecialchars($order['customer_name'] ?? 'Umum') ?></span>
                <span>Metode: TUNAI</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- List Item Pesanan -->
        <div>
            <?php foreach ($order['items'] as $item): ?>
                <div style="margin-bottom: 4px;">
                    <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                    <div class="item-row">
                        <span><?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?></span>
                        <span class="font-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php if (!empty($item['notes'])): ?>
                        <div class="item-note">*(<?= htmlspecialchars($item['notes']) ?>)</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="divider-double"></div>

        <!-- Perhitungan Total & Pembayaran -->
        <div style="font-size: 11px;">
            <div class="summary-row">
                <span>TOTAL</span>
                <span class="font-bold">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>BAYAR (TUNAI)</span>
                <span>Rp <?= number_format($order['cash_paid'], 0, ',', '.') ?></span>
            </div>
            <div class="summary-row font-bold">
                <span>KEMBALI</span>
                <span>Rp <?= number_format($order['cash_change'], 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Footer Struk -->
        <div class="text-center" style="font-size: 10px; margin-top: 6px;">
            <p><?= htmlspecialchars($settings['receipt_footer'] ?? 'Terima Kasih Atas Kunjungan Anda! Selamat Menikmati.') ?></p>
            <p style="margin-top: 4px; font-size: 9px; opacity: 0.8;">Simpan struk ini sebagai bukti pembayaran sah.</p>
        </div>

    </div>

    <!-- Script auto-print jika dibuka dari kasir -->
    <?php if (isset($_GET['autoprint'])): ?>
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
    <?php endif; ?>

</body>
</html>
