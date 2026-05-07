<?php
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

requireAdmin();

$pdo = getDatabaseConnection();

$stmt = $pdo->query(
    "SELECT books.*, genres.name AS genre_name, users.username
     FROM books
     JOIN genres ON books.genre_id = genres.id
     LEFT JOIN users ON books.user_id = users.id
     ORDER BY books.created_at DESC"
);

$books = $stmt->fetchAll();
?>

<h1>Управление книгами</h1>

<a class="btn" href="/bookhub/books/create.php">Добавить книгу</a>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Автор</th>
        <th>Жанр</th>
        <th>Год</th>
        <th>Статус</th>
        <th>Добавил</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= (int)$book['id'] ?></td>
            <td><?= e($book['title']) ?></td>
            <td><?= e($book['author']) ?></td>
            <td><?= e($book['genre_name']) ?></td>
            <td><?= (int)$book['year'] ?></td>
            <td><?= $book['status'] === 'available' ? 'Доступна' : 'Недоступна' ?></td>
            <td><?= e($book['username'] ?? 'Неизвестно') ?></td>
            <td>
                <div class="actions">
                    <a class="btn btn-warning" href="/bookhub/books/edit.php?id=<?= (int)$book['id'] ?>">Редактировать</a>
                    <a class="btn btn-danger"
                       onclick="return confirm('Удалить книгу?')"
                       href="/bookhub/books/delete.php?id=<?= (int)$book['id'] ?>">Удалить</a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
