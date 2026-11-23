<?php

declare(strict_types=1);

function userProfile(PDO $pdo, string $email): array
{
    $stmt = $pdo->prepare('SELECT email, full_name, phone, address FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    return is_array($row) ? $row : ['email' => $email, 'full_name' => '', 'phone' => '', 'address' => ''];
}

function userValidateProfile(array $data): array
{
    $errors = [];

    if ($data['full_name'] !== '' && mb_strlen($data['full_name'], 'UTF-8') < 2) {
        $errors['full_name'] = 'Имя должно содержать не менее 2 символов.';
    }
    if ($data['phone'] !== '' && !preg_match('/^[\d\s()+\-]{6,20}$/', $data['phone'])) {
        $errors['phone'] = 'Укажите корректный номер телефона.';
    }
    if ($data['address'] !== '' && mb_strlen($data['address'], 'UTF-8') < 5) {
        $errors['address'] = 'Адрес должен содержать не менее 5 символов.';
    }

    return $errors;
}

function userUpdateProfile(PDO $pdo, string $email, array $data): void
{
    $stmt = $pdo->prepare('UPDATE users SET full_name = ?, phone = ?, address = ? WHERE email = ?');
    $stmt->execute([$data['full_name'], $data['phone'], $data['address'], $email]);
}

function userValidatePassword(PDO $pdo, string $email, string $current, string $new, string $confirmation): ?string
{
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $hash = $stmt->fetchColumn();

    if (!is_string($hash) || !password_verify($current, $hash)) {
        return 'Текущий пароль указан неверно.';
    }
    if (strlen($new) < 6 || strlen($new) > 72) {
        return 'Новый пароль должен содержать от 6 до 72 символов.';
    }
    if ($new !== $confirmation) {
        return 'Пароли не совпадают.';
    }

    return null;
}

function userUpdatePassword(PDO $pdo, string $email, string $new): void
{
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $email]);
}
