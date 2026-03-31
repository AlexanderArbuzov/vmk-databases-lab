<?php

declare(strict_types=1);

namespace App\Domain\Store;

class Order
{
    private int $id;
    private iterable $items;

    public function __construct(int $id, iterable $items) {
        $this->id = $id;
        $this->items = [...$items];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

//    public int $totalCount {
//        get => array_sum(array_column($this->items, 'count'));
//    }

//    public int $totalPrice {
//        get => array_sum(
//            array_map(fn(OrderItem $item) => $item->price * $item->count, $this->items)
//        );
//    }

    public function addItem(Product $product, int $count): void
    {
        $sku = (int)$product->getSku();
        $item = $this->findItemByProductSku($sku);

        if ($item === null) {
            $this->items[] = new OrderItem($sku, $count, $product->getPrice());
        } else {
            $this->removeItem($item);
            $this->items[] = new OrderItem($sku, $item->getCount() + $count, $product->getPrice());
        }
    }

    private function findItemByProductSku(int $sku): ?OrderItem
    {
        foreach ($this->items as $item) {
            if ($item->getProductId() == $sku) {
                return $item;
            }
        }
        return null;
    }

    private function removeItem(OrderItem $target): void
    {
        $this->items = array_filter(
            $this->items,
            fn($item) => $item !== $target
        );
    }
}
