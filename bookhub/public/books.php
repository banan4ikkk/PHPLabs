<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();

$stmt = $pdo->query(
    "SELECT books.*, genres.name AS genre_name
     FROM books
     JOIN genres ON books.genre_id = genres.id
     ORDER BY books.created_at DESC"
);

$books = $stmt->fetchAll();
?>

<h1>Каталог книг</h1>

<?php if (empty($books)): ?>
    <p>Книги пока не добавлены.</p>
<?php endif; ?>

<div class="grid">
    <?php foreach ($books as $book): ?>
        <div class="card">
            <h3><?= e($book['title']) ?></h3>
            <p><strong>Автор:</strong> <?= e($book['author']) ?></p>
            <p><strong>Жанр:</strong> <?= e($book['genre_name']) ?></p>
            <p><strong>Год:</strong> <?= (int)$book['year'] ?></p>
            <p><strong>Статус:</strong> <?= $book['status'] === 'available' ? 'Доступна' : 'Недоступна' ?></p>
            <a class="btn" href="/bookhub/public/book.php?id=<?= (int)$book['id'] ?>">Подробнее</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
