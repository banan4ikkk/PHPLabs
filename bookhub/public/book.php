<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT books.*, genres.name AS genre_name, users.username
     FROM books
     JOIN genres ON books.genre_id = genres.id
     LEFT JOIN users ON books.user_id = users.id
     WHERE books.id = :id"
);

$stmt->execute([':id' => $id]);
$book = $stmt->fetch();
?>

<?php if (!$book): ?>
    <h1>Книга не найдена</h1>
    <p>Такой книги нет в базе данных.</p>
<?php else: ?>
    <div class="card">
        <h1><?= e($book['title']) ?></h1>
        <p><strong>Автор:</strong> <?= e($book['author']) ?></p>
        <p><strong>Жанр:</strong> <?= e($book['genre_name']) ?></p>
        <p><strong>Год:</strong> <?= (int)$book['year'] ?></p>
        <p><strong>Статус:</strong> <?= $book['status'] === 'available' ? 'Доступна' : 'Недоступна' ?></p>
        <p><strong>Добавил:</strong> <?= e($book['username'] ?? 'Неизвестно') ?></p>
        <p><?= nl2br(e($book['description'])) ?></p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
