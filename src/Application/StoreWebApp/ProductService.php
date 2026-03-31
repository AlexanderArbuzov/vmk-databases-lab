<?php

declare(strict_types=1);

namespace App\Application\StoreWebApp;

use App\Domain\Store\IProductRepository;
use App\Domain\Store\Product;

class ProductService
{
    private IProductRepository $productRepository;

    public function __construct(IProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }
    public function getAllProductsByQuery(string $query): array {
        if (Product::isSku($query))
            return $this->productRepository->getAllBySku($query);

        return $this->productRepository->getAllByName($query);
    }

    public function getProductById(int $id): Product {
        return $this->productRepository->getById($id);
    }
}
