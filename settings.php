<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';
require __DIR__ . '/app/users.php';

$authError = handleAuthRequest($pdo);

if (!isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$email = (string) $_SESSION['user_email'];
$profileErrors = [];
$passwordError = null;
$form = userProfile($pdo, $email);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['settings_action'] ?? '') === 'profile') {
    foreach (['full_name', 'phone', 'address'] as $field) {
        $form[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    $profileErrors = userValidateProfile($form);
    if ($profileErrors === []) {
        userUpdateProfile($pdo, $email, $form);
        catalogFlash('Данные профиля сохранены.');
        header('Location: settings.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['settings_action'] ?? '') === 'password') {
    $passwordError = userValidatePassword(
        $pdo,
        $email,
        (string) ($_POST['current_password'] ?? ''),
        (string) ($_POST['new_password'] ?? ''),
        (string) ($_POST['new_password_confirmation'] ?? '')
    );

    if ($passwordError === null) {
        userUpdatePassword($pdo, $email, (string) $_POST['new_password']);
        catalogFlash('Пароль изменён.');
        header('Location: settings.php');
        exit;
    }
}

$flash = consumeCatalogFlash();

$pageTitle = 'Настройки — PC Builder';
$pageDescription = 'Настройки профиля PC Builder: изменение имени, телефона, адреса доставки и пароля.';
require __DIR__ . '/app/views/header.php';
?>

<main class="simple-page container settings-page">
    <div class="simple-page__heading">
        <div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><a href="profile.php">Личный кабинет</a><span>›</span><span>Настройки</span></div>
        <h1>Настройки</h1>
        <p>Измените свои данные. Email используется для входа, поэтому изменить его нельзя.</p>
    </div>

    <?php if ($flash): ?><div class="flash flash--<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <section class="checkout-fields settings-card">
        <h2 class="checkout-section__title">Личные данные</h2>
        <form method="post" novalidate>
            <input type="hidden" name="settings_action" value="profile">
            <label class="checkout-field">
                <span>Email</span>
                <input type="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" disabled>
            </label>
            <label class="checkout-field<?= isset($profileErrors['full_name']) ? ' checkout-field--error' : '' ?>">
                <span>Имя и фамилия</span>
                <input type="text" name="full_name" value="<?= htmlspecialchars((string) $form['full_name'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Иван Иванов" maxlength="120">
                <?php if (isset($profileErrors['full_name'])): ?><em><?= $profileErrors['full_name'] ?></em><?php endif; ?>
            </label>
            <label class="checkout-field<?= isset($profileErrors['phone']) ? ' checkout-field--error' : '' ?>">
                <span>Телефон</span>
                <input type="tel" name="phone" value="<?= htmlspecialchars((string) $form['phone'], ENT_QUOTES, 'UTF-8') ?>" placeholder="+7 (900) 000-00-00" maxlength="20">
                <?php if (isset($profileErrors['phone'])): ?><em><?= $profileErrors['phone'] ?></em><?php endif; ?>
            </label>
            <label class="checkout-field<?= isset($profileErrors['address']) ? ' checkout-field--error' : '' ?>">
                <span>Адрес доставки</span>
                <input type="text" name="address" value="<?= htmlspecialchars((string) $form['address'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Город, улица, дом, квартира" maxlength="255">
                <?php if (isset($profileErrors['address'])): ?><em><?= $profileErrors['address'] ?></em><?php endif; ?>
            </label>
            <button type="submit" class="button button--primary settings-submit">Сохранить</button>
        </form>
    </section>

    <section class="checkout-fields settings-card">
        <h2 class="checkout-section__title">Смена пароля</h2>
        <?php if ($passwordError !== null): ?><div class="flash flash--error"><?= htmlspecialchars($passwordError, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form method="post" novalidate>
            <input type="hidden" name="settings_action" value="password">
            <label class="checkout-field">
                <span>Текущий пароль</span>
                <input type="password" name="current_password" autocomplete="current-password" maxlength="72" required>
            </label>
            <div class="checkout-grid">
                <label class="checkout-field">
                    <span>Новый пароль</span>
                    <input type="password" name="new_password" autocomplete="new-password" maxlength="72" required>
                </label>
                <label class="checkout-field">
                    <span>Повторите новый пароль</span>
                    <input type="password" name="new_password_confirmation" autocomplete="new-password" maxlength="72" required>
                </label>
            </div>
            <button type="submit" class="button button--primary settings-submit">Изменить пароль</button>
        </form>
    </section>
</main>

<footer class="site-footer">
    <div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div>
</footer>
</body>

</html>
