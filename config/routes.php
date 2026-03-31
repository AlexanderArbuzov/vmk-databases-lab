<?php

declare(strict_types=1);

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('home', new Route(
    '/',
    ['_controller' => [App\Presentation\Controllers\ProductController::class, 'index']],
    [],
    [],
    '',
    [],
    ['GET']
));

$routes->add('cart_new', new \Symfony\Component\Routing\Route(
    '/cart/new',
    ['_controller' => [App\Presentation\Controllers\CartController::class, 'new']],
    [],
    [],
    '',
    [],
    ['POST']
));

$routes->add('products_index', new Route(
    '/products',
    ['_controller' => [App\Presentation\Controllers\ProductController::class, 'index']],
    [],
    [],
    '',
    [],
    ['GET']
));

$routes->add('cart_show', new Route(
    '/cart',
    ['_controller' => [App\Presentation\Controllers\CartController::class, 'show']],
    [],
    [],
    '',
    [],
    ['GET']
));

$routes->add('cart_add', new Route(
    '/cart/add',
    ['_controller' => [App\Presentation\Controllers\CartController::class, 'add']],
    [],
    [],
    '',
    [],
    ['POST']
));

$routes->add('cart_confirm', new Route(
    '/cart/confirm',
    ['_controller' => [App\Presentation\Controllers\CartController::class, 'confirm']],
    [],
    [],
    '',
    [],
    ['POST']
));

$routes->add('orders_index', new Route(
    '/orders',
    ['_controller' => [App\Presentation\Controllers\OrderController::class, 'index']],
    [],
    [],
    '',
    [],
    ['GET']
));

$routes->add('orders_cancel', new Route(
    '/orders/{id}/cancel',
    ['_controller' => [App\Presentation\Controllers\OrderController::class, 'cancel']],
    ['id' => '\d+'],
    [],
    '',
    [],
    ['POST']
));

return $routes;
