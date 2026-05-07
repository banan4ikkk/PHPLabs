<?php
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../includes/header.php';

requireAdmin();
?>

<h1>Админ-панель</h1>

<div class="grid">
    <div class="card">
        <h3>Пользователи</h3>
        <p>Просмотр всех пользователей и создание новых администраторов.</p>
        <a class="btn" href="/bookhub/admin/users.php">Управлять пользователями</a>
    </div>

    <div class="card">
        <h3>Книги</h3>
        <p>Просмотр, редактирование и удаление книг.</p>
        <a class="btn" href="/bookhub/admin/books.php">Управлять книгами</a>
    </div>

    <div class="card">
        <h3>Создать администратора</h3>
        <p>Добавление новой учётной записи с ролью администратора.</p>
        <a class="btn" href="/bookhub/admin/create_admin.php">Создать</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
