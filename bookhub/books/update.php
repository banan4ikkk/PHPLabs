<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();

$pdo = getDatabaseConnection();

$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    redirect('/bookhub/public/books.php');
}

if (!isAdmin() && (int)$book['user_id'] !== (int)$_SESSION['user_id']) {
    redirect('/bookhub/public/books.php');
}

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$genreId = (int)($_POST['genre_id'] ?? 0);
$year = (int)($_POST['year'] ?? 0);
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? '';

if (
    strlen($title) < 2 ||
    strlen($author) < 2 ||
    $genreId <= 0 ||
    $year < 1800 ||
    $year > (int)date('Y') ||
    strlen($description) < 10 ||
    !in_array($status, ['available', 'unavailable'], true)
) {
    redirect('/bookhub/books/edit.php?id=' . $id);
}

$updateStmt = $pdo->prepare(
    "UPDATE books
     SET title = :title,
         author = :author,
         genre_id = :genre_id,
         year = :year,
         description = :description,
         status = :status
     WHERE id = :id"
);

$updateStmt->execute([
    ':title' => $title,
    ':author' => $author,
    ':genre_id' => $genreId,
    ':year' => $year,
    ':description' => $description,
    ':status' => $status,
    ':id' => $id
]);

redirect('/bookhub/public/books.php');
