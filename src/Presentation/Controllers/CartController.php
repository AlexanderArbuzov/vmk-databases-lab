<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\StoreWebApp\OrderService;
use App\Application\StoreWebApp\ProductService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

final class CartController
{
    public function __construct(
        private OrderService $orderService,
        private ProductService $productService
    ) {}

    public function new(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $order = $this->orderService->createOrder();
        $request->getSession()->set('cart_order_id', $order->getId());

        return new \Symfony\Component\HttpFoundation\RedirectResponse('/products');
    }
    private function getCurrentOrderId(Session $session): int
    {
        $orderId = $session->get('cart_order_id');
        if (!$orderId) {
            $order = $this->orderService->createOrder();
            $orderId = $order->getId();
            $session->set('cart_order_id', $orderId);
        }
        return (int)$orderId;
    }

    public function show(Request $request): Response
    {
        $session = $request->getSession();
        $orderId = $this->getCurrentOrderId($session);
        $order = $this->orderService->getOrderById($orderId);

        ob_start();
        ?>
        <h1>Корзина #<?= $order->getId() ?></h1>
        <p><a href="/products">Товары</a> | <a href="/orders">Заказы</a></p>
        <table border="1" cellpadding="6">
            <tr><th>SKU</th><th>Количество</th><th>Цена</th><th>Сумма</th></tr>
            <?php $total = 0; foreach ($order->getItems() as $i): $line = $i->getCount() * $i->getPrice(); $total += $line; ?>
                <tr>
                    <td><?= $i->getProductId() ?></td>
                    <td><?= $i->getCount() ?></td>
                    <td><?= $i->getPrice() ?></td>
                    <td><?= $line ?></td>
                </tr>
            <?php endforeach; ?>
            <tr><td colspan="3"><b>Итого</b></td><td><b><?= $total ?></b></td></tr>
        </table>
        <form method="post" action="/cart/confirm">
            <button type="submit">Подтвердить заказ</button>
        </form>
        <?php
        return new Response((string)ob_get_clean());
    }

    public function add(Request $request): Response
    {
        $session = $request->getSession();
        $orderId = $this->getCurrentOrderId($session);

        $productId = (int)$request->request->get('product_id');
        $qty = max(1, (int)$request->request->get('qty', 1));

        $order = $this->orderService->getOrderById($orderId);
        $product = $this->productService->getProductById($productId);

        $order->addItem($product, $qty);
        $this->orderService->updateOrder($order);

        return new RedirectResponse('/cart');
    }

    public function confirm(Request $request): Response
    {
        $session = $request->getSession();
        $orderId = $this->getCurrentOrderId($session);
        $order = $this->orderService->getOrderById($orderId);

        $this->orderService->confirmOrder($order);

        $newOrder = $this->orderService->createOrder();
        $session->set('cart_order_id', $newOrder->getId());

        return new RedirectResponse('/orders');
    }
}
