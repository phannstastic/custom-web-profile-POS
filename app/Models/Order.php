<?php

namespace App\Models;

class Order extends Model
{
    protected string $table = 'orders';

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $stmt = static::getDb()->prepare("
            SELECT invoice_number FROM orders 
            WHERE invoice_number LIKE :prefix 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([':prefix' => $prefix . '%']);
        $lastInvoice = $stmt->fetchColumn();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice, -4);
            $newNumber = str_pad((string)($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public function createCashOrder(array $orderData, array $items): array
    {
        $db = static::getDb();
        $db->beginTransaction();

        try {
            $invoiceNumber = $this->generateInvoiceNumber();
            $customerName = !empty($orderData['customer_name']) ? trim($orderData['customer_name']) : 'Pelanggan';
            $tableNumber = !empty($orderData['table_number']) ? trim($orderData['table_number']) : 'Meja 01';
            $totalAmount = (int) ($orderData['total_amount'] ?? 0);
            $cashPaid = (int) ($orderData['cash_paid'] ?? $totalAmount);
            $cashChange = max(0, $cashPaid - $totalAmount);
            $notes = !empty($orderData['notes']) ? trim($orderData['notes']) : null;

            $stmtOrder = $db->prepare("
                INSERT INTO orders (
                    invoice_number, customer_name, table_number, total_amount, 
                    payment_method, payment_status, cash_paid, cash_change, notes
                ) VALUES (
                    :invoice, :customer, :table_num, :total, 
                    'cash', 'paid', :paid, :change, :notes
                )
            ");

            $stmtOrder->execute([
                ':invoice'   => $invoiceNumber,
                ':customer'  => $customerName,
                ':table_num' => $tableNumber,
                ':total'     => $totalAmount,
                ':paid'      => $cashPaid,
                ':change'    => $cashChange,
                ':notes'     => $notes
            ]);

            $orderId = (int) $db->lastInsertId();

            $stmtItem = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, price, quantity, subtotal, notes
                ) VALUES (
                    :order_id, :product_id, :product_name, :price, :quantity, :subtotal, :notes
                )
            ");

            foreach ($items as $item) {
                $stmtItem->execute([
                    ':order_id'     => $orderId,
                    ':product_id'   => !empty($item['id']) ? (int) $item['id'] : null,
                    ':product_name' => $item['name'],
                    ':price'        => (int) $item['price'],
                    ':quantity'     => (int) $item['qty'],
                    ':subtotal'     => (int) ($item['price'] * $item['qty']),
                    ':notes'        => !empty($item['notes']) ? trim($item['notes']) : null
                ]);
            }

            $db->commit();

            return [
                'success' => true,
                'order_id' => $orderId,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customerName,
                'table_number' => $tableNumber,
                'total_amount' => $totalAmount,
                'cash_paid' => $cashPaid,
                'cash_change' => $cashChange,
                'created_at' => date('Y-m-d H:i:s')
            ];

        } catch (\Throwable $e) {
            $db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createPendingQrisOrder(array $orderData, array $items): array
    {
        $db = static::getDb();
        $db->beginTransaction();

        try {
            $invoiceNumber = $this->generateInvoiceNumber();
            $customerName = !empty($orderData['customer_name']) ? trim($orderData['customer_name']) : 'Pelanggan';
            $tableNumber = !empty($orderData['table_number']) ? trim($orderData['table_number']) : 'Meja 01';
            $totalAmount = (int) ($orderData['total_amount'] ?? 0);
            $notes = !empty($orderData['notes']) ? trim($orderData['notes']) : null;

            $stmtOrder = $db->prepare("
                INSERT INTO orders (
                    invoice_number, customer_name, table_number, total_amount, 
                    payment_method, payment_status, cash_paid, cash_change, notes
                ) VALUES (
                    :invoice, :customer, :table_num, :total, 
                    'qris', 'pending', 0, 0, :notes
                )
            ");

            $stmtOrder->execute([
                ':invoice'   => $invoiceNumber,
                ':customer'  => $customerName,
                ':table_num' => $tableNumber,
                ':total'     => $totalAmount,
                ':notes'     => $notes
            ]);

            $orderId = (int) $db->lastInsertId();

            $stmtItem = $db->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, price, quantity, subtotal, notes
                ) VALUES (
                    :order_id, :product_id, :product_name, :price, :quantity, :subtotal, :notes
                )
            ");

            foreach ($items as $item) {
                $stmtItem->execute([
                    ':order_id'     => $orderId,
                    ':product_id'   => !empty($item['id']) ? (int) $item['id'] : null,
                    ':product_name' => $item['name'],
                    ':price'        => (int) $item['price'],
                    ':quantity'     => (int) $item['qty'],
                    ':subtotal'     => (int) ($item['price'] * $item['qty']),
                    ':notes'        => !empty($item['notes']) ? trim($item['notes']) : null
                ]);
            }

            $db->commit();

            return [
                'success' => true,
                'order_id' => $orderId,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customerName,
                'table_number' => $tableNumber,
                'total_amount' => $totalAmount,
                'created_at' => date('Y-m-d H:i:s')
            ];

        } catch (\Throwable $e) {
            $db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function setSnapToken(int $orderId, string $token): void
    {
        $stmt = static::getDb()->prepare("UPDATE orders SET snap_token = :token WHERE id = :id");
        $stmt->execute([':token' => $token, ':id' => $orderId]);
    }

    public function updateStatusByInvoice(string $invoiceNumber, string $status): bool
    {
        $stmt = static::getDb()->prepare("
            UPDATE orders 
            SET payment_status = :status,
                cash_paid = CASE WHEN :status = 'paid' THEN total_amount ELSE cash_paid END,
                updated_at = CURRENT_TIMESTAMP
            WHERE invoice_number = :invoice
        ");
        return $stmt->execute([':status' => $status, ':invoice' => $invoiceNumber]);
    }

    public function findByInvoice(string $invoiceNumber): ?array
    {
        $stmt = static::getDb()->prepare("SELECT * FROM orders WHERE invoice_number = :invoice LIMIT 1");
        $stmt->execute([':invoice' => $invoiceNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getOrderWithItems(int $orderId): ?array
    {
        $stmtOrder = static::getDb()->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmtOrder->execute([':id' => $orderId]);
        $order = $stmtOrder->fetch();

        if (!$order) {
            return null;
        }

        $stmtItems = static::getDb()->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmtItems->execute([':order_id' => $orderId]);
        $order['items'] = $stmtItems->fetchAll();

        return $order;
    }

    /**
     * Hitung ringkasan penjualan (omset, cash vs qris, total transaksi) berdasarkan rentang tanggal
     */
    public function getSalesSummary(?string $startDate = null, ?string $endDate = null, ?string $paymentMethod = null, ?string $paymentStatus = null): array
    {
        $where = [];
        $params = [];

        if (!empty($startDate)) {
            $where[] = "date(created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $where[] = "date(created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }

        if (!empty($paymentMethod) && $paymentMethod !== 'all') {
            $where[] = "payment_method = :payment_method";
            $params[':payment_method'] = $paymentMethod;
        }

        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            $where[] = "payment_status = :payment_status";
            $params[':payment_status'] = $paymentStatus;
        }

        $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // Query metrik omset (khusus status 'paid' untuk pendapatan riil, serta total hitungan semua order)
        $sql = "
            SELECT 
                COUNT(*) as total_transactions,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as total_paid_transactions,
                SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN payment_status = 'paid' AND payment_method = 'cash' THEN total_amount ELSE 0 END) as total_cash_revenue,
                SUM(CASE WHEN payment_status = 'paid' AND payment_method = 'qris' THEN total_amount ELSE 0 END) as total_qris_revenue,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as total_pending_transactions,
                SUM(CASE WHEN payment_status = 'cancelled' THEN 1 ELSE 0 END) as total_cancelled_transactions
            FROM orders
            {$whereSql}
        ";

        $stmt = static::getDb()->prepare($sql);
        $stmt->execute($params);
        $summary = $stmt->fetch();

        return [
            'total_transactions' => (int) ($summary['total_transactions'] ?? 0),
            'total_paid_transactions' => (int) ($summary['total_paid_transactions'] ?? 0),
            'total_revenue' => (int) ($summary['total_revenue'] ?? 0),
            'total_cash_revenue' => (int) ($summary['total_cash_revenue'] ?? 0),
            'total_qris_revenue' => (int) ($summary['total_qris_revenue'] ?? 0),
            'total_pending_transactions' => (int) ($summary['total_pending_transactions'] ?? 0),
            'total_cancelled_transactions' => (int) ($summary['total_cancelled_transactions'] ?? 0),
        ];
    }

    /**
     * Dapatkan daftar order dengan filter pencarian dan item-itemnya
     */
    public function getFilteredOrders(?string $startDate = null, ?string $endDate = null, ?string $paymentMethod = null, ?string $paymentStatus = null, ?string $search = null): array
    {
        $where = [];
        $params = [];

        if (!empty($startDate)) {
            $where[] = "date(o.created_at) >= :start_date";
            $params[':start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $where[] = "date(o.created_at) <= :end_date";
            $params[':end_date'] = $endDate;
        }

        if (!empty($paymentMethod) && $paymentMethod !== 'all') {
            $where[] = "o.payment_method = :payment_method";
            $params[':payment_method'] = $paymentMethod;
        }

        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            $where[] = "o.payment_status = :payment_status";
            $params[':payment_status'] = $paymentStatus;
        }

        if (!empty($search)) {
            $where[] = "(o.invoice_number LIKE :search OR o.customer_name LIKE :search OR o.table_number LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT o.*, 
                   COUNT(oi.id) as total_items_count,
                   GROUP_CONCAT(oi.product_name || ' (' || oi.quantity || 'x)', ', ') as items_summary
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            {$whereSql}
            GROUP BY o.id
            ORDER BY o.created_at DESC, o.id DESC
        ";

        $stmt = static::getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
