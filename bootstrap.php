<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__.'/.env')) {
    (new Dotenv())->usePutenv()->loadEnv(__DIR__.'/.env');
}

$container = new ContainerBuilder();

// Явно прокидываем значения из окружения в параметры контейнера
$container->setParameter('database_dsn', $_ENV['DATABASE_DSN'] ?? $_SERVER['DATABASE_DSN'] ?? getenv('DATABASE_DSN'));
$container->setParameter('database_user', $_ENV['DATABASE_USER'] ?? $_SERVER['DATABASE_USER'] ?? getenv('DATABASE_USER'));
$container->setParameter('database_password', $_ENV['DATABASE_PASSWORD'] ?? $_SERVER['DATABASE_PASSWORD'] ?? getenv('DATABASE_PASSWORD'));

$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/config'));
$loader->load('services.yaml');

$container->compile(true);

return $container;
