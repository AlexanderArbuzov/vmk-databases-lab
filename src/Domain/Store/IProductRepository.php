<?php

declare(strict_types=1);

namespace App\Domain\Store;

interface IProductRepository
{
    public function getAllBySku(string $namePart): array;
    public function getAllByName(string $namePart): array;
    public function getById(int $id): Product;
}
