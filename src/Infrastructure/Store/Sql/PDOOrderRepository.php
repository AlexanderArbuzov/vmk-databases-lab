<?php

declare(strict_types=1);

namespace App\Infrastructure\Store\Sql;

use App\Domain\Store\IOrderRepository;
use App\Domain\Store\Order;
use App\Domain\Store\OrderItem;
use PDO;

class PDOOrderRepository implements IOrderRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(): Order
    {
        $stmt = $this->pdo->prepare('INSERT INTO orders (total_sum) VALUES (0)');
        $stmt->execute();

        $id = (int)$this->pdo->lastInsertId();

        return new Order($id, []);
    }

    public function confirm(Order $order): void {
        $orderId = $order->getId();
        $items = $order->getItems();

        $this->pdo->beginTransaction();
        try {
            $total = 0.0;
            foreach ($items as $item) {
                $total += $item->getPrice() * $item->getCount();
            }

            $upd = $this->pdo->prepare('UPDATE orders SET total_sum = :total WHERE id = :id');
            $upd->execute([
                'id' => $orderId,
                'total' => $total,
            ]);

            $ins = $this->pdo->prepare(
                'INSERT INTO order_items (id, sku, quantity)
                 VALUES (:order_id, :sku, :quantity)
                 ON CONFLICT (id, sku)
                 DO UPDATE SET quantity = EXCLUDED.quantity'
            );

            foreach ($items as $item) {
                $ins->execute([
                    'order_id' => $orderId,
                    'sku' => $item->getProductId(), // в текущей схеме OrderItem.productId == products.sku
                    'quantity' => (int)$item->getCount(),
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getById(int $id): Order
    {
        $stmt = $this->pdo->prepare('SELECT id FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException("Order with id {$id} not found");
        }

        $itemStmt = $this->pdo->prepare(
            'SELECT oi.sku, oi.quantity, p.price
             FROM order_items oi
             INNER JOIN products p ON p.sku = oi.sku
             WHERE oi.id = :order_id'
        );
        $itemStmt->execute(['order_id' => $id]);

        $itemsRows = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        $items = array_map(
            fn(array $r) => new OrderItem(
                (int)$r['sku'],
                (float)$r['quantity'],
                (float)$r['price']
            ),
            $itemsRows
        );

        return new Order((int)$row['id'], $items);
    }

    public function update(Order $order): void
    {
        $orderId = $order->getId();
        $items = $order->getItems();

        $this->pdo->beginTransaction();
        try {
            $total = 0.0;
            foreach ($items as $item) {
                $total += $item->getPrice() * $item->getCount();
            }

            $upd = $this->pdo->prepare('UPDATE orders SET total_sum = :total WHERE id = :id');
            $upd->execute([
                'id' => $orderId,
                'total' => $total,
            ]);

            $del = $this->pdo->prepare('DELETE FROM order_items WHERE id = :order_id');
            $del->execute(['order_id' => $orderId]);

            $ins = $this->pdo->prepare(
                'INSERT INTO order_items (id, sku, quantity)
                 VALUES (:order_id, :sku, :quantity)'
            );

            foreach ($items as $item) {
                $ins->execute([
                    'order_id' => $orderId,
                    'sku' => $item->getProductId(), // в текущей схеме OrderItem.productId == products.sku
                    'quantity' => (int)$item->getCount(),
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
