<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

requireAuth();

$pdo = getDatabaseConnection();
$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();
?>

<h1>Добавить книгу</h1>

<form class="form" action="/bookhub/books/store.php" method="POST" data-book-form>
    <label>Название книги:</label>
    <input type="text" name="title" required minlength="2" maxlength="150">

    <label>Автор:</label>
    <input type="text" name="author" required minlength="2" maxlength="100">

    <label>Жанр:</label>
    <select name="genre_id" required>
        <option value="">Выберите жанр</option>
        <?php foreach ($genres as $genre): ?>
            <option value="<?= (int)$genre['id'] ?>"><?= e($genre['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Год издания:</label>
    <input type="number" name="year" min="1800" max="<?= date('Y') ?>" required>

    <label>Описание:</label>
    <textarea name="description" required minlength="10"></textarea>

    <label>Статус:</label>
    <select name="status" required>
        <option value="available">Доступна</option>
        <option value="unavailable">Недоступна</option>
    </select>

    <button type="submit">Добавить</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
