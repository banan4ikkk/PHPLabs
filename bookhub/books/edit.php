<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

requireAuth();

$pdo = getDatabaseConnection();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    echo '<h1>Книга не найдена</h1>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

if (!isAdmin() && (int)$book['user_id'] !== (int)$_SESSION['user_id']) {
    echo '<div class="error">У вас нет прав редактировать эту книгу.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();
?>

<h1>Редактировать книгу</h1>

<form class="form" action="/bookhub/books/update.php" method="POST" data-book-form>
    <input type="hidden" name="id" value="<?= (int)$book['id'] ?>">

    <label>Название книги:</label>
    <input type="text" name="title" required minlength="2" maxlength="150" value="<?= e($book['title']) ?>">

    <label>Автор:</label>
    <input type="text" name="author" required minlength="2" maxlength="100" value="<?= e($book['author']) ?>">

    <label>Жанр:</label>
    <select name="genre_id" required>
        <?php foreach ($genres as $genre): ?>
            <option value="<?= (int)$genre['id'] ?>" <?= (int)$genre['id'] === (int)$book['genre_id'] ? 'selected' : '' ?>>
                <?= e($genre['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Год издания:</label>
    <input type="number" name="year" min="1800" max="<?= date('Y') ?>" required value="<?= (int)$book['year'] ?>">

    <label>Описание:</label>
    <textarea name="description" required minlength="10"><?= e($book['description']) ?></textarea>

    <label>Статус:</label>
    <select name="status" required>
        <option value="available" <?= $book['status'] === 'available' ? 'selected' : '' ?>>Доступна</option>
        <option value="unavailable" <?= $book['status'] === 'unavailable' ? 'selected' : '' ?>>Недоступна</option>
    </select>

    <button type="submit">Сохранить</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
