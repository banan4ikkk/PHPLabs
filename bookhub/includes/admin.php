<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * Запрещает доступ пользователям без роли администратора.
 *
 * @return void
 */
function requireAdmin(): void
{
    requireAuth();

    if (!isAdmin()) {
        redirect('/bookhub/public/index.php');
    }
}
