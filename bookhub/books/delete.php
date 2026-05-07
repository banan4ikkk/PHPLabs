<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();

$pdo = getDatabaseConnection();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    redirect('/bookhub/public/books.php');
}

if (!isAdmin() && (int)$book['user_id'] !== (int)$_SESSION['user_id']) {
    redirect('/bookhub/public/books.php');
}

$deleteStmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
$deleteStmt->execute([':id' => $id]);

redirect('/bookhub/public/books.php');
