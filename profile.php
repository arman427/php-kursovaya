<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';

$authError = handleAuthRequest($pdo);

if (empty($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Личный кабинет — PC Builder';
require __DIR__ . '/app/views/header.php';
?>

<main class="profile-page container">
    <svg class="svg-sprite" aria-hidden="true">
        <symbol id="icon-search" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="6.5"></circle>
            <path d="m16 16 4.5 4.5"></path>
        </symbol>
        <symbol id="icon-heart" viewBox="0 0 24 24">
            <path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path>
        </symbol>
        <symbol id="icon-user" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="3.5"></circle>
            <path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path>
        </symbol>
        <symbol id="icon-cart" viewBox="0 0 24 24">
            <path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path>
            <circle cx="9" cy="19.5" r="1.2"></circle>
            <circle cx="17" cy="19.5" r="1.2"></circle>
        </symbol>
    </svg>

    <section class="profile-card" aria-labelledby="profile-title">
        <p class="eyebrow"><span></span> Аккаунт</p>
        <h1 id="profile-title">Личный кабинет</h1>
        <p class="profile-card__email"><?= htmlspecialchars((string) $_SESSION['user_email'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="profile-card__text">Здесь будут ваши заказы, избранные товары и настройки аккаунта.</p>
        <a class="button button--primary" href="index.php">Вернуться в магазин</a>
    </section>
</main>

<footer class="site-footer">
    <div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div>
</footer>
</body>

</html>
