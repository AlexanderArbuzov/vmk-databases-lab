<?php

declare(strict_types=1);

namespace App\Infrastructure\Store\Memory;

use App\Domain\Store\IOrderRepository;
use App\Domain\Store\Order;

class OrderRepository implements IOrderRepository
{
    private array $orders = [];
    public function create(): Order
    {
        $nextId = count($this->orders) + 1;
        echo PHP_EOL . PHP_EOL . PHP_EOL . "nextId " . $nextId . PHP_EOL;
        $order = new Order($nextId, []);

        $this->orders[] = $order;

        var_dump($this->orders);

        return $order;
    }

    public function confirm(Order $order): void
    {
        // TODO: Implement confirm() method.
    }

    public function getById(int $id): Order {
        $results = array_filter($this->orders, fn($order) => $order->id === $id);

        if (empty($results)) {
            throw new Exception("Order with ID $id not found");
        }

        return reset($results);
    }

    public function update(Order $order): void {
        // TODO: Implement confirm() method.
    }
}
