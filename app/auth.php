<?php

declare(strict_types=1);

function handleAuthRequest(PDO $pdo): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['auth_action'])) {
        return null;
    }

    $action = (string) $_POST['auth_action'];

    if ($action === 'logout') {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: index.php');
        exit;
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный email.';
    }
    if (strlen($password) < 6 || strlen($password) > 72) {
        return 'Пароль должен содержать от 6 до 72 символов.';
    }

    if ($action === 'register') {
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');
        if ($password !== $confirmation) {
            return 'Пароли не совпадают.';
        }

        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) {
            return 'Пользователь с таким email уже зарегистрирован.';
        }

        $insert = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $insert->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['user_email'] = $email;
        header('Location: index.php');
        exit;
    }

    if ($action === 'login') {
        $find = $pdo->prepare('SELECT email, password_hash FROM users WHERE email = ? LIMIT 1');
        $find->execute([$email]);
        $user = $find->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return 'Неверный email или пароль.';
        }

        $_SESSION['user_email'] = $user['email'];
        header('Location: index.php');
        exit;
    }

    return 'Неизвестное действие.';
}
