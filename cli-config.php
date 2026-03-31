<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    (new Dotenv())->usePutenv()->loadEnv(__DIR__ . '/.env');
}

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = (int)($_ENV['DB_PORT'] ?? 5432);
$db   = $_ENV['DB_NAME'] ?? 'magicnumbers';
$user = $_ENV['DATABASE_USER'] ?? 'magicnumbers';
$pass = $_ENV['DATABASE_PASSWORD'] ?? 'password';

$connection = DriverManager::getConnection([
    'driver'   => 'pdo_pgsql',
    'host'     => $host,
    'port'     => $port,
    'dbname'   => $db,
    'user'     => $user,
    'password' => $pass,
    'charset'  => 'utf8',
]);

return DependencyFactory::fromConnection(
    new PhpFile(__DIR__ . '/migrations.php'),
    new ExistingConnection($connection)
);
