<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();

$pdo = getDatabaseConnection();

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$genreId = (int)($_POST['genre_id'] ?? 0);
$year = (int)($_POST['year'] ?? 0);
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? '';
$userId = (int)$_SESSION['user_id'];

$errors = [];

if (strlen($title) < 2 || strlen($title) > 150) {
    $errors[] = 'Название книги должно содержать от 2 до 150 символов.';
}

if (strlen($author) < 2 || strlen($author) > 100) {
    $errors[] = 'Имя автора должно содержать от 2 до 100 символов.';
}

if ($genreId <= 0) {
    $errors[] = 'Выберите жанр.';
}

if ($year < 1800 || $year > (int)date('Y')) {
    $errors[] = 'Введите корректный год издания.';
}

if (strlen($description) < 10) {
    $errors[] = 'Описание должно содержать минимум 10 символов.';
}

if (!in_array($status, ['available', 'unavailable'], true)) {
    $errors[] = 'Некорректный статус книги.';
}

if (!empty($errors)) {
    require_once __DIR__ . '/../includes/header.php';

    echo '<h1>Ошибка добавления книги</h1>';

    foreach ($errors as $error) {
        echo '<div class="error">' . e($error) . '</div>';
    }

    echo '<a class="btn" href="/bookhub/books/create.php">Вернуться назад</a>';

    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO books (title, author, genre_id, year, description, status, user_id)
     VALUES (:title, :author, :genre_id, :year, :description, :status, :user_id)"
);

$stmt->execute([
    ':title' => $title,
    ':author' => $author,
    ':genre_id' => $genreId,
    ':year' => $year,
    ':description' => $description,
    ':status' => $status,
    ':user_id' => $userId
]);

redirect('/bookhub/public/books.php');
