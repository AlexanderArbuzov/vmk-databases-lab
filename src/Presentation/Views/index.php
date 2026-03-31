<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php';

/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$container = require $_SERVER['DOCUMENT_ROOT'] . '/bootstrap.php';

$routes = require_once $_SERVER['DOCUMENT_ROOT'] . '/config/routes.php';

$request = Request::createFromGlobals();
$session = new Session();
$session->start();
$request->setSession($session);

$context = (new RequestContext())->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    $params = $matcher->match($request->getPathInfo());
    $controllerDef = $params['_controller'];
    unset($params['_controller'], $params['_route']);

    [$class, $method] = $controllerDef;
    $controller = $container->get($class);

    $args = [$request];
    if (isset($params['id'])) {
        $args[] = (int)$params['id'];
    }

    /** @var Response $response */
    $response = $controller->$method(...$args);
} catch (\Throwable $e) {
    $response = new Response(
        'Not Found / Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString(),
        500
    );
}

$response->send();
