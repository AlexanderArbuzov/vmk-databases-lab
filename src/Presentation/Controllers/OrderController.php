<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use PDO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderController
{
    public function __construct(private PDO $pdo) {}

    public function index(Request $request): Response
    {
        $rows = $this->pdo->query(
            'SELECT id, total_sum FROM orders ORDER BY id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        ?>
        <h1>Оформленные заказы</h1>
        <p><a href="/products">Товары</a> | <a href="/cart">Корзина</a></p>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Сумма</th><th>Действие</th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= (float)$r['total_sum'] ?></td>
                    <td>
                        <form method="post" action="/orders/<?= (int)$r['id'] ?>/cancel">
                            <button type="submit">Отменить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
        return new Response((string)ob_get_clean());
    }

    public function cancel(Request $request, int $id): Response
    {
        $stmt = $this->pdo->prepare('DELETE FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]); // order_items удалятся каскадом
        return new RedirectResponse('/orders');
    }
}
