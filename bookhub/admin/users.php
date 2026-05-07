<?php
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

requireAdmin();

$pdo = getDatabaseConnection();

$stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<h1>Пользователи</h1>

<a class="btn" href="/bookhub/admin/create_admin.php">Создать администратора</a>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Дата создания</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= (int)$user['id'] ?></td>
            <td><?= e($user['username']) ?></td>
            <td><?= e($user['email']) ?></td>
            <td><?= e($user['role']) ?></td>
            <td><?= e($user['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
