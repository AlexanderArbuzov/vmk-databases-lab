<?php

declare(strict_types=1);

namespace App\Domain\Store;

interface IOrderRepository
{
    public function create(): Order;

    public function confirm(Order $order): void;

    public function getById(int $id): Order;

    public function update(Order $order): void;
}
