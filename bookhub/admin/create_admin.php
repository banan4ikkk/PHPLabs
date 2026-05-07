<?php
require_once __DIR__ . '/../includes/admin.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

requireAdmin();

$pdo = getDatabaseConnection();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3) {
        $errors[] = 'Имя администратора должно содержать минимум 3 символа.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов.';
    }

    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $checkStmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);

        if ($checkStmt->fetch()) {
            $errors[] = 'Такой пользователь уже существует.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, role)
             VALUES (:username, :email, :password, 'admin')"
        );

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $success = 'Администратор успешно создан.';
    }
}
?>

<h1>Создать администратора</h1>

<form class="form" method="POST">
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="success"><?= e($success) ?></div>
    <?php endif; ?>

    <label>Имя администратора:</label>
    <input type="text" name="username" required minlength="3">

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required minlength="6">

    <button type="submit">Создать администратора</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
