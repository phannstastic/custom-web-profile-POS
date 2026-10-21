<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    private function initMidtrans(): array
    {
        $db = Connection::get();
        $settingsRaw = $db->query("SELECT key, value FROM settings WHERE key LIKE 'midtrans_%'")->fetchAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['key']] = $row['value'];
        }

        $serverKey = $settings['midtrans_server_key'] ?? '';
        $clientKey = $settings['midtrans_client_key'] ?? '';
        $isProduction = ($settings['midtrans_is_production'] ?? '0') === '1';

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        return [
            'server_key' => $serverKey,
            'client_key' => $clientKey,
            'is_production' => $isProduction
        ];
    }

    public function createSnap(): void
    {
        $config = $this->initMidtrans();

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload || empty($payload['items'])) {
            $this->json(['success' => false, 'error' => 'Data pesanan kosong'], 400);
        }

        if (empty($config['server_key'])) {
            $this->json([
                'success' => false, 
                'error' => 'Midtrans Server Key belum diatur di menu Pengaturan Admin.'
            ], 400);
        }

        $orderModel = new Order();
        $created = $orderModel->createPendingQrisOrder($payload, $payload['items']);

        if (!$created['success']) {
            $this->json(['success' => false, 'error' => $created['error'] ?? 'Gagal membuat pesanan'], 500);
        }

        // Siapkan Item Details untuk Midtrans
        $itemDetails = [];
        foreach ($payload['items'] as $item) {
            $itemDetails[] = [
                'id' => (string) $item['id'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['qty'],
                'name' => mb_strimwidth($item['name'], 0, 45, '...')
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $created['invoice_number'],
                'gross_amount' => (int) $created['total_amount']
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $created['customer_name'] ?: 'Pelanggan',
                'phone' => '08123456789'
            ],
            // Khususkan metode pembayaran QRIS & E-Wallet
            'enabled_payments' => ['qris', 'gopay', 'shopeepay']
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $orderModel->setSnapToken($created['order_id'], $snapToken);

            $this->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $created['order_id'],
                'invoice_number' => $created['invoice_number'],
                'total_amount' => $created['total_amount']
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'error' => 'Midtrans Snap Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(string $invoiceNumber): void
    {
        $orderModel = new Order();
        $order = $orderModel->findByInvoice($invoiceNumber);

        if (!$order) {
            $this->json(['success' => false, 'error' => 'Order tidak ditemukan'], 404);
        }

        $this->json([
            'success' => true,
            'invoice_number' => $order['invoice_number'],
            'order_id' => $order['id'],
            'payment_status' => $order['payment_status'],
            'payment_method' => $order['payment_method'],
            'receipt_url' => '/pos/receipt/' . $order['id'] . '?autoprint=1'
        ]);
    }

    public function webhook(): void
    {
        $config = $this->initMidtrans();
        $raw = file_get_contents('php://input');
        $notif = json_decode($raw, true);

        if (!$notif || empty($notif['order_id']) || empty($notif['status_code'])) {
            $this->json(['status' => 'error', 'message' => 'Invalid webhook payload'], 400);
        }

        $orderId = $notif['order_id'];
        $statusCode = $notif['status_code'];
        $grossAmount = $notif['gross_amount'];
        $signatureKey = $notif['signature_key'] ?? '';
        $transactionStatus = $notif['transaction_status'] ?? '';
        $fraudStatus = $notif['fraud_status'] ?? '';

        // Validasi Signature Key
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $config['server_key']);

        if ($signatureKey !== $mySignature) {
            $this->json(['status' => 'error', 'message' => 'Invalid signature key'], 403);
        }

        $orderModel = new Order();

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $orderModel->updateStatusByInvoice($orderId, 'challenge');
            } else if ($fraudStatus == 'accept') {
                $orderModel->updateStatusByInvoice($orderId, 'paid');
            }
        } else if ($transactionStatus == 'settlement') {
            // QRIS Settlement Sukses
            $orderModel->updateStatusByInvoice($orderId, 'paid');
        } else if ($transactionStatus == 'pending') {
            $orderModel->updateStatusByInvoice($orderId, 'pending');
        } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $orderModel->updateStatusByInvoice($orderId, 'cancelled');
        }

        $this->json(['status' => 'success', 'message' => 'Webhook notification processed']);
    }
}
