<?php

declare(strict_types=1);

namespace App\Application\StoreWebApp;

use App\Domain\Store\IOrderRepository;
use App\Domain\Store\Order;

class OrderService
{
    private IOrderRepository $orderRepository;

    public function __construct(IOrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(): Order {
        return $this->orderRepository->create();
    }

    public function confirmOrder(Order $order): void {
        $this->orderRepository->confirm($order);
    }

    public function getOrderById(int $id): Order
    {
        return $this->orderRepository->getById($id);
    }

    public function updateOrder(Order $order): void {
        $this->orderRepository->update($order);
    }
}
