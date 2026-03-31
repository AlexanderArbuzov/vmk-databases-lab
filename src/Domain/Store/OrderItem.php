<?php

declare(strict_types=1);

namespace App\Domain\Store;

class OrderItem
{
    private int $productId;
    private float $count;
    private float $price;

    public function __construct(int $productId, float $count, float $price) {
        if ($count <= 0)
            throw new \InvalidArgumentException('Count must be greater than 0');

        $this->productId = $productId;
        $this->count = $count;
        $this->price = $price;
    }

    public function getProductId(): int {
        return $this->productId;
    }

    public function getCount(): float {
        return $this->count;
    }

    public function getPrice():float {
        return $this->price;
    }
}
