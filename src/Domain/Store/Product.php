<?php

declare(strict_types=1);

namespace App\Domain\Store;

class Product
{
    private int $id;
    private string $sku;
    private string $name;
    private float $price;

    public function __construct(int $id, string $sku, string $name, float $price) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): int {
        return $this->id;
    }
    public function getSku(): string {
        return $this->sku;
    }
    public static function isSku(string $s): bool {
        $s = str_replace(['-', ' '], '', $s);

        return (bool) preg_match('/\d{5}/', $s);
    }
    public function getName(): string {
        return $this->name;
    }

    public function getPrice(): float {
        return $this->price;
    }
}
