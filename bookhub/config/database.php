<?php

declare(strict_types=1);

/**
 * Возвращает подключение к базе данных через PDO.
 *
 * @return PDO Объект подключения к базе данных.
 */
function getDatabaseConnection(): PDO
{
    $host = 'localhost';
    $databaseName = 'bookhub';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$databaseName};charset=utf8mb4",
            $username,
            $password
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    } catch (PDOException $exception) {
        die('Ошибка подключения к базе данных: ' . $exception->getMessage());
    }
}
