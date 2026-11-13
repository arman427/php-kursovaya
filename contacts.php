<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';

$authError = handleAuthRequest($pdo);
$pageTitle = 'Контакты — PC Builder';
require __DIR__ . '/app/views/header.php';
?>
<main class="simple-page container contacts-page">
    <svg class="svg-sprite" aria-hidden="true"><symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol><symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol><symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol><symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol></svg>
    <div class="simple-page__heading"><div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><span>Контакты</span></div><h1>Контакты</h1><p>Поможем выбрать комплектующие и собрать идеальный ПК.</p></div>
    <div class="contacts-grid"><article><h2>Поддержка</h2><a href="mailto:hello@pc-builder.local">hello@pc-builder.local</a><a href="tel:+78005553535">8 800 555-35-35</a><span>Ежедневно с 9:00 до 21:00</span></article><article><h2>Шоурум</h2><p>Москва, ул. Техническая, 12</p><span>Самовывоз и консультация по сборке</span></article><article><h2>Доставка</h2><p>По всей России</p><span>Отправляем заказ в день оформления</span></article></div>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
</body>
</html>
