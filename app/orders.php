<?php

declare(strict_types=1);

const ORDER_STATUS_NEW = 'new';
const ORDER_STATUS_PENDING_PAYMENT = 'pending_payment';
const ORDER_STATUS_CONFIRMED = 'confirmed';

function orderStatusLabel(string $status): string
{
    return match ($status) {
        ORDER_STATUS_PENDING_PAYMENT => 'Ожидает оплаты',
        ORDER_STATUS_CONFIRMED => 'Оплата при получении',
        default => 'Новый',
    };
}

function orderStatusClass(string $status): string
{
    return match ($status) {
        ORDER_STATUS_PENDING_PAYMENT => 'order-status--pending',
        ORDER_STATUS_CONFIRMED => 'order-status--cash',
        default => 'order-status--new',
    };
}

function orderPaymentLabel(string $method): string
{
    return $method === 'cash' ? 'Оплата при получении' : 'Банковская карта';
}

function orderFormatNumber(int $id): string
{
    return 'PC-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
}

function orderLuhnValid(string $number): bool
{
    $number = preg_replace('/\D+/', '', $number) ?? '';
    $length = strlen($number);
    if ($length < 13 || $length > 19) {
        return false;
    }

    $sum = 0;
    for ($i = 0; $i < $length; $i++) {
        $digit = (int) $number[$length - 1 - $i];
        if ($i % 2 === 1) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }

    return $sum % 10 === 0;
}

function orderCreate(PDO $pdo, array $items, array $data, float $total): array
{
    $pdo->beginTransaction();
    try {
        $statement = $pdo->prepare(
            'INSERT INTO orders (order_number, user_email, customer_name, phone, address, comment, payment_method, status, total)
             VALUES (:order_number, :user_email, :customer_name, :phone, :address, :comment, :payment_method, :status, :total)'
        );
        $statement->execute([
            'order_number' => 'TMP-' . bin2hex(random_bytes(4)),
            'user_email' => (string) $data['user_email'],
            'customer_name' => (string) $data['customer_name'],
            'phone' => (string) $data['phone'],
            'address' => (string) $data['address'],
            'comment' => (string) ($data['comment'] ?? ''),
            'payment_method' => (string) $data['payment_method'],
            'status' => (string) $data['status'],
            'total' => $total,
        ]);

        $orderId = (int) $pdo->lastInsertId();
        $orderNumber = orderFormatNumber($orderId);
        $pdo->prepare('UPDATE orders SET order_number = ? WHERE id = ?')->execute([$orderNumber, $orderId]);

        $itemsStatement = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity)
             VALUES (:order_id, :product_id, :product_name, :product_image, :price, :quantity)'
        );
        foreach ($items as $item) {
            $itemsStatement->execute([
                'order_id' => $orderId,
                'product_id' => $item['id'] !== '' ? $item['id'] : null,
                'product_name' => (string) $item['name'],
                'product_image' => (string) $item['image_path'],
                'price' => (float) $item['price'],
                'quantity' => (int) $item['quantity'],
            ]);
        }

        $pdo->commit();

        return ['id' => $orderId, 'number' => $orderNumber];
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function orderUserHistory(PDO $pdo, string $email): array
{
    $orders = [];
    try {
        $statement = $pdo->prepare('SELECT id, order_number, status, payment_method, total, created_at FROM orders WHERE user_email = ? ORDER BY id DESC');
        $statement->execute([$email]);
        $rows = $statement->fetchAll();

        if ($rows === []) {
            return [];
        }

        $itemsStatement = $pdo->prepare(
            'SELECT product_name, product_image, price, quantity FROM order_items WHERE order_id = ? ORDER BY id ASC'
        );

        foreach ($rows as $row) {
            $itemsStatement->execute([(int) $row['id']]);
            $row['items'] = $itemsStatement->fetchAll();
            $orders[] = $row;
        }
    } catch (PDOException) {
        
        return [];
    }

    return $orders;
}
