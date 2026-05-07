<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDatabaseConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (empty($password)) {
        $errors[] = 'Введите пароль.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Неверный email или пароль.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            redirect('/bookhub/public/index.php');
        }
    }
}
?>

<h1>Вход</h1>

<form class="form" method="POST">
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <button type="submit">Войти</button>
</form>

<p>Администратор по умолчанию: <strong>admin@example.com</strong>, пароль: <strong>admin123</strong></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
