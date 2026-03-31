<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\StoreWebApp\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProductController
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): Response
    {
        $q = trim((string)$request->query->get('q', ''));
        $products = $q === ''
            ? $this->productService->getAllProductsByQuery('') // или отдельный метод "all"
            : $this->productService->getAllProductsByQuery($q);

        ob_start();
        ?>
        <h1>Товары</h1>
        <form method="get" action="/products">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Поиск по имени/sku">
            <button type="submit">Найти</button>
        </form>
        <p><a href="/cart">Корзина</a> | <a href="/orders">Заказы</a></p>
        <form method="post" action="/cart/new" style="margin: 12px 0;">
            <button type="submit">Создать новый заказ</button>
        </form>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>SKU</th><th>Название</th><th>Цена</th><th>В корзину</th></tr>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p->getId() ?></td>
                    <td><?= htmlspecialchars($p->getSku()) ?></td>
                    <td><?= htmlspecialchars($p->getName()) ?></td>
                    <td><?= $p->getPrice() ?></td>
                    <td>
                        <form method="post" action="/cart/add">
                            <input type="hidden" name="product_id" value="<?= $p->getId() ?>">
                            <input type="number" name="qty" min="1" value="1" required>
                            <button type="submit">Добавить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
        return new Response((string)ob_get_clean());
    }
}
