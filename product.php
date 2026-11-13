<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';

$authError = handleAuthRequest($pdo);
$productId = trim((string) ($_GET['id'] ?? ''));
$product = $productId !== '' ? catalogProduct($pdo, $productId) : null;
if ($product === null) {
    http_response_code(404);
    $pageTitle = 'Товар не найден — PC Builder';
} else {
    $pageTitle = $product['name'] . ' — PC Builder';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product !== null) {
    $action = (string) ($_POST['store_action'] ?? '');
    if ($action === 'add_cart') {
        addCartItem((string) $product['id'], max(1, (int) ($_POST['quantity'] ?? 1)));
        catalogFlash('Товар добавлен в корзину.');
    } elseif ($action === 'toggle_favorite') {
        $added = toggleFavorite((string) $product['id']);
        catalogFlash($added ? 'Товар добавлен в избранное.' : 'Товар удалён из избранного.');
    }
    catalogRedirectBack('product.php?id=' . urlencode((string) $product['id']));
}

$flash = consumeCatalogFlash();
require __DIR__ . '/app/views/header.php';
?>
<main class="simple-page container product-page">
    <svg class="svg-sprite" aria-hidden="true"><symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol><symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol><symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol><symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol></svg>
    <?php if ($product === null): ?><div class="catalog-empty"><h1>Товар не найден</h1><p>Возможно, товар был удалён или ссылка устарела.</p><a href="catalog.php" class="button button--primary">Вернуться в каталог</a></div><?php else: ?>
        <div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><a href="catalog.php?category=<?= urlencode((string) $product['category_slug']) ?>"><?= htmlspecialchars((string) catalogCategory((string) $product['category_slug'])['title'], ENT_QUOTES, 'UTF-8') ?></a><span>›</span><span><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php if ($flash): ?><div class="flash flash--<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <section class="product-detail"><div class="product-detail__visual"><span class="badge <?= htmlspecialchars($product['badge_class'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?></span><img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"></div><div class="product-detail__info"><p class="product-detail__brand"><?= htmlspecialchars((string) $product['brand'], ENT_QUOTES, 'UTF-8') ?></p><h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1><div class="rating rating--large"><span>★</span> <?= number_format((float) $product['rating'], 1, ',', ' ') ?><small><?= number_format((int) $product['reviews_count'], 0, ',', ' ') ?> отзывов</small></div><p class="product-detail__description"><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p><div class="product-detail__stock"><span></span><?= (int) $product['in_stock'] === 1 ? 'В наличии' : 'Под заказ' ?> · Доставка от 1 дня</div><div class="product-detail__buy"><strong><?= catalogPrice((float) $product['price']) ?></strong><form method="post" class="product-buy-form"><input type="hidden" name="store_action" value="add_cart"><label>Количество<input type="number" name="quantity" value="1" min="1" max="99"></label><button class="button button--primary" type="submit"><svg class="icon"><use href="#icon-cart"></use></svg> Добавить в корзину</button></form></div><form method="post"><input type="hidden" name="store_action" value="toggle_favorite"><button class="favorite-link <?= isFavorite((string) $product['id']) ? 'is-active' : '' ?>" type="submit"><svg class="icon"><use href="#icon-heart"></use></svg> <?= isFavorite((string) $product['id']) ? 'В избранном' : 'Добавить в избранное' ?></button></form></div></section>
        <section class="product-info-panel"><h2>О товаре</h2><div><span>Категория</span><strong><?= htmlspecialchars((string) catalogCategory((string) $product['category_slug'])['title'], ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Бренд</span><strong><?= htmlspecialchars((string) $product['brand'], ENT_QUOTES, 'UTF-8') ?></strong></div><div><span>Гарантия</span><strong>12 месяцев</strong></div></section>
    <?php endif; ?>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
</body>
</html>
