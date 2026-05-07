<?php

declare(strict_types=1);

/**
 * Экранирует строку перед выводом в HTML.
 *
 * @param string|null $value Значение для вывода.
 * @return string Безопасная строка.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Перенаправляет пользователя на другую страницу.
 *
 * @param string $path Путь для перехода.
 * @return void
 */
function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

/**
 * Проверяет, является ли текущий пользователь авторизованным.
 *
 * @return bool true, если пользователь вошёл в систему.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Проверяет, является ли текущий пользователь администратором.
 *
 * @return bool true, если роль пользователя admin.
 */
function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
