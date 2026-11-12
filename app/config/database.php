<?php

declare(strict_types=1);

$host = '127.0.0.1';
$dbName = 'pc_builder';
$dbUser = 'root';
$dbPassword = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Не удалось подключиться к базе данных. Проверьте настройки в app/config/database.php.');
}
