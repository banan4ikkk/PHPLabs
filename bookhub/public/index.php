<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();

$stmt = $pdo->query(
    "SELECT books.*, genres.name AS genre_name
     FROM books
     JOIN genres ON books.genre_id = genres.id
     ORDER BY books.created_at DESC
     LIMIT 3"
);

$books = $stmt->fetchAll();
?>

<h1>Добро пожаловать в BookHub</h1>

<p>
    BookHub — это веб-приложение для просмотра и управления каталогом книг.
    Здесь можно искать книги, добавлять новые записи и управлять данными через админ-панель.
</p>

<h2>Последние добавленные книги</h2>

<div class="grid">
    <?php foreach ($books as $book): ?>
        <div class="card">
            <h3><?= e($book['title']) ?></h3>
            <p><strong>Автор:</strong> <?= e($book['author']) ?></p>
            <p><strong>Жанр:</strong> <?= e($book['genre_name']) ?></p>
            <p><?= e(mb_substr($book['description'], 0, 120)) ?>...</p>
            <a class="btn" href="/bookhub/public/book.php?id=<?= (int)$book['id'] ?>">Подробнее</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
