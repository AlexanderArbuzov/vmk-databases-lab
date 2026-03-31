<?php

declare(strict_types=1);

namespace App\Infrastructure\Store\Memory;
use App\Domain\Store\Product;
use App\Domain\Store\IProductRepository;

class ProductRepository implements IProductRepository
{
    private array $products;
    public function __construct()
    {
        $this->products = [
            new Product(1,"11111", "ba", 2.5),
            new Product(2,"11112", "bb", 2.7),
            new Product(3,"11113", "bc", 2.9),
        ];
    }

    public function getAllBySku(string $sku): array {
        return array_filter(
            $this->products,
            fn(Product $product) => $product->getSku() === $sku
        );
    }
    public function getAllByName(string $namePart): array
    {
        return array_filter(
            $this->products,
            fn(Product $product) => str_contains($product->getName(), $namePart)
        );
    }

    public function getById(int $id): Product {
        return array_find(
            $this->products,
            fn(Product $product) => $product->getId() == $id);
    }
}
