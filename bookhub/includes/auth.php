<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

/**
 * Запрещает доступ неавторизованным пользователям.
 *
 * @return void
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect('/bookhub/public/login.php');
    }
}
