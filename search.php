<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';

$query = trim((string) ($_GET['q'] ?? ''));
$products = catalogProducts($pdo);
if ($query !== '') {
    $products = array_values(array_filter($products, static fn (array $product): bool => mb_stripos($product['name'], $query, 0, 'UTF-8') !== false));
}

$pageTitle = $query !== '' ? 'Поиск: ' . $query . ' — PC Builder' : 'Поиск товаров — PC Builder';
$flash = consumeCatalogFlash();
require __DIR__ . '/app/views/header.php';
?>
<main class="simple-page container">
    <svg class="svg-sprite" aria-hidden="true">
        <symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol>
        <symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol>
        <symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol>
        <symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol>
    </svg>
    <div class="simple-page__heading"><div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><span>Поиск</span></div><h1><?= $query !== '' ? 'Результаты поиска' : 'Поиск товаров' ?></h1><p><?= $query !== '' ? 'По запросу «' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . '» найдено: ' . count($products) : 'Введите название комплектующего в строку поиска.' ?></p></div>
    <?php if ($flash): ?><div class="flash flash--<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($products): ?>
        <div class="simple-products-grid">
            <?php foreach ($products as $product): ?>
                <article class="catalog-product-card"><div class="catalog-product-card__top"><span class="badge <?= htmlspecialchars($product['badge_class'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?></span><form method="post" action="catalog.php" class="inline-action"><input type="hidden" name="store_action" value="toggle_favorite"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'search.php', ENT_QUOTES, 'UTF-8') ?>"><button class="favorite-button <?= isFavorite((string) $product['id']) ? 'is-active' : '' ?>" type="submit" aria-label="Избранное"><svg class="icon"><use href="#icon-heart"></use></svg></button></form></div><a class="catalog-product-card__visual" href="product.php?id=<?= urlencode((string) $product['id']) ?>"><img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"></a><h3><a href="product.php?id=<?= urlencode((string) $product['id']) ?>"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></a></h3><div class="rating"><span>★</span> <?= number_format((float) $product['rating'], 1, ',', ' ') ?><small>(<?= number_format((int) $product['reviews_count'], 0, ',', ' ') ?> отзывов)</small></div><div class="catalog-product-card__bottom"><strong><?= catalogPrice((float) $product['price']) ?></strong><form method="post" action="catalog.php" class="inline-action"><input type="hidden" name="store_action" value="add_cart"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'search.php', ENT_QUOTES, 'UTF-8') ?>"><button class="add-cart add-cart--icon" type="submit" aria-label="В корзину"><svg class="icon"><use href="#icon-cart"></use></svg><span>В корзину</span></button></form></div></article>
            <?php endforeach; ?>
        </div>
    <?php elseif ($query !== ''): ?><div class="catalog-empty"><h2>Ничего не найдено</h2><p>Попробуйте другой запрос или перейдите в полный каталог.</p><a href="catalog.php" class="button button--primary">Открыть каталог</a></div><?php endif; ?>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
</body>
</html>
