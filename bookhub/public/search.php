<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();

$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();

$query = trim($_GET['query'] ?? '');
$genreId = $_GET['genre_id'] ?? '';

$sql = "SELECT books.*, genres.name AS genre_name
        FROM books
        JOIN genres ON books.genre_id = genres.id
        WHERE 1=1";

$params = [];

if ($query !== '') {
    $sql .= " AND (books.title LIKE :query OR books.author LIKE :query)";
    $params[':query'] = '%' . $query . '%';
}

if ($genreId !== '') {
    $sql .= " AND books.genre_id = :genre_id";
    $params[':genre_id'] = (int)$genreId;
}

$sql .= " ORDER BY books.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<h1>Поиск книг</h1>

<form class="form" method="GET">
    <label>Название или автор:</label>
    <input type="text" name="query" value="<?= e($query) ?>" placeholder="Например: Orwell">

    <label>Жанр:</label>
    <select name="genre_id">
        <option value="">Все жанры</option>
        <?php foreach ($genres as $genre): ?>
            <option value="<?= (int)$genre['id'] ?>" <?= (string)$genre['id'] === (string)$genreId ? 'selected' : '' ?>>
                <?= e($genre['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Найти</button>
</form>

<h2>Результаты поиска</h2>

<?php if (empty($books)): ?>
    <p>Ничего не найдено.</p>
<?php endif; ?>

<?php foreach ($books as $book): ?>
    <div class="card">
        <h3><?= e($book['title']) ?></h3>
        <p><strong>Автор:</strong> <?= e($book['author']) ?></p>
        <p><strong>Жанр:</strong> <?= e($book['genre_name']) ?></p>
        <a class="btn" href="/bookhub/public/book.php?id=<?= (int)$book['id'] ?>">Подробнее</a>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
