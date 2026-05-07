<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (strlen($username) < 3) {
        $errors[] = 'Имя пользователя должно содержать минимум 3 символа.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов.';
    }

    if ($password !== $passwordConfirmation) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $checkStmt->execute([
            ':email' => $email,
            ':username' => $username
        ]);

        if ($checkStmt->fetch()) {
            $errors[] = 'Пользователь с таким email или именем уже существует.';
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, role)
             VALUES (:username, :email, :password, 'user')"
        );

        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        redirect('/bookhub/public/login.php');
    }
}
?>

<h1>Регистрация</h1>

<form class="form" method="POST">
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <label>Имя пользователя:</label>
    <input type="text" name="username" required minlength="3">

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required minlength="6">

    <label>Повторите пароль:</label>
    <input type="password" name="password_confirmation" required minlength="6">

    <button type="submit">Зарегистрироваться</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
