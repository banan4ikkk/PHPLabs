<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>BookHub </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/bookhub/assets/css/style.css?v=999">

<style>
    html,
    body {
        margin: 0 !important;
        min-height: 100vh !important;
    }

    body {
        display: flex !important;
        flex-direction: column !important;
        background: #f4f6f8 !important;
    }

    main.container {
        flex: 1 0 auto !important;
        padding-bottom: 80px !important;
    }

    .site-footer {
        margin-top: auto !important;
        background: #1f2937 !important;
        color: white !important;
        padding: 22px 0 !important;
        width: 100% !important;
    }

    .site-footer p {
        margin: 0 !important;
    }
</style>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="/bookhub/public/index.php">BookHub</a>

        <nav class="nav">
            <a href="/bookhub/public/index.php">Главная</a>
            <a href="/bookhub/public/books.php">Каталог</a>
            <a href="/bookhub/public/search.php">Поиск</a>

            <?php if (isLoggedIn()): ?>
                <a href="/bookhub/books/create.php">Добавить книгу</a>

                <?php if (isAdmin()): ?>
                    <a href="/bookhub/admin/dashboard.php">Админка</a>
                <?php endif; ?>

                <a href="/bookhub/public/logout.php">Выход</a>
            <?php else: ?>
                <a href="/bookhub/public/login.php">Вход</a>
                <a href="/bookhub/public/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
