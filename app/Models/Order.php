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
}
