<?php

declare(strict_types=1);

namespace App\Infrastructure\Store\Sql;

use App\Domain\Store\IProductRepository;
use App\Domain\Store\Product;
use PDO;

class PDOProductRepository implements IProductRepository
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllBySku(string $sku): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, sku, name, price
             FROM products
             WHERE sku = :sku'
        );
        $stmt->execute(['sku' => (int)$sku]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $r) => new Product(
                (int)$r['id'],
                (string)$r['sku'],
                (string)$r['name'],
                (float)$r['price']
            ),
            $rows
        );
    }

    public function getAllByName(string $namePart): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, sku, name, price
             FROM products
             WHERE name ILIKE :name'
        );
        $stmt->execute(['name' => '%' . $namePart . '%']);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $r) => new Product(
                (int)$r['id'],
                (string)$r['sku'],
                (string)$r['name'],
                (float)$r['price']
            ),
            $rows
        );
    }

    public function getById(int $id): Product
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, sku, name, price
             FROM products
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException("Product with id {$id} not found");
        }

        return new Product(
            (int)$row['id'],
            (string)$row['sku'],
            (string)$row['name'],
            (float)$row['price']
        );
    }
}
