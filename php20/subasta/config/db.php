<?php

require_once __DIR__ . '/env.php';

function db_connect(): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'] ?? 'db',
        $_ENV['DB_NAME'] ?? 'subastas'
    );

    $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? '', $_ENV['DB_PASS'] ?? '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}
