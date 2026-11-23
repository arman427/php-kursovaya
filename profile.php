<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';
require __DIR__ . '/app/orders.php';

$authError = handleAuthRequest($pdo);

if (empty($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$orders = orderUserHistory($pdo, (string) $_SESSION['user_email']);

$pageTitle = 'Личный кабинет — PC Builder';
$pageDescription = 'Личный кабинет PC Builder: история заказов и настройки профиля покупателя.';
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
        <p class="profile-card__text">Здесь ваши заказы, избранные товары и настройки аккаунта.</p>
        <a class="button button--primary" href="index.php">Вернуться в магазин</a>
    </section>

    <section class="profile-orders" id="orders" aria-labelledby="orders-title">
        <h2 id="orders-title">Мои заказы</h2>
        <?php if ($orders === []): ?>
            <div class="catalog-empty profile-orders__empty">
                <h3>Заказов пока нет</h3>
                <p>Оформите первый заказ — он появится в этом списке.</p>
                <a href="catalog.php" class="button button--primary">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <article class="order-card">
                    <header class="order-card__head">
                        <div>
                            <strong class="order-card__number">Заказ <?= htmlspecialchars((string) $order['order_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="order-card__date"><?= htmlspecialchars(date('d.m.Y H:i', strtotime((string) $order['created_at'])), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="order-card__meta">
                            <span class="order-status <?= htmlspecialchars(orderStatusClass((string) $order['status']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(orderStatusLabel((string) $order['status']), ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="order-card__payment"><?= htmlspecialchars(orderPaymentLabel((string) $order['payment_method']), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </header>
                    <ul class="order-card__items">
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <img src="<?= htmlspecialchars(catalogImagePath((string) ($item['product_image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" alt="" width="48" height="48">
                                <span class="order-card__name"><?= htmlspecialchars((string) $item['product_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="order-card__qty">× <?= (int) $item['quantity'] ?></span>
                                <strong><?= catalogPrice((float) $item['price'] * (int) $item['quantity']) ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <footer class="order-card__foot">
                        <span>Итого</span>
                        <strong><?= catalogPrice((float) $order['total']) ?></strong>
                    </footer>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<footer class="site-footer">
    <div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div>
</footer>
</body>

</html>
