<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';

$pageTitle = 'Каталог товаров — PC Builder';
$authError = handleAuthRequest($pdo);

$action = (string) ($_POST['store_action'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== '') {
    $productId = trim((string) ($_POST['product_id'] ?? ''));
    $product = $productId !== '' ? catalogProduct($pdo, $productId) : null;

    if ($action === 'add_cart' && $product !== null) {
        addCartItem((string) $product['id']);
        catalogFlash('Товар добавлен в корзину.');
    } elseif ($action === 'toggle_favorite' && $product !== null) {
        $isAdded = toggleFavorite((string) $product['id']);
        catalogFlash($isAdded ? 'Товар добавлен в избранное.' : 'Товар удалён из избранного.');
    } elseif ($action === 'remove_favorite' && $product !== null) {
        removeFavorite((string) $product['id']);
        catalogFlash('Товар удалён из избранного.');
    }

    catalogRedirectBack('catalog.php');
}

$searchQuery = trim((string) ($_GET['q'] ?? ''));
$categorySlug = (string) ($_GET['category'] ?? 'all');
if (!catalogCategory($categorySlug)) {
    $categorySlug = 'all';
}
$sort = (string) ($_GET['sort'] ?? 'popular');
$allowedSorts = ['popular', 'price_asc', 'price_desc', 'rating_desc', 'name_asc'];
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'popular';
}
$minPrice = max(0, (int) ($_GET['min_price'] ?? 0));
$maxPrice = (int) ($_GET['max_price'] ?? 200000);
$maxPrice = $maxPrice > 0 ? min($maxPrice, 1000000) : 200000;
$brandFilters = array_values(array_filter(array_map('strval', (array) ($_GET['brand'] ?? []))));
$stockOnly = isset($_GET['in_stock']) && (string) $_GET['in_stock'] === '1';
$ratingFilter = (float) ($_GET['rating'] ?? 0);
if (!in_array($ratingFilter, [0.0, 4.0, 4.5, 4.8], true)) {
    $ratingFilter = 0.0;
}

$allProducts = catalogProducts($pdo);
$brands = [];
foreach ($allProducts as $product) {
    $brands[$product['brand']] = ($brands[$product['brand']] ?? 0) + 1;
}
ksort($brands, SORT_NATURAL | SORT_FLAG_CASE);

$products = array_values(array_filter($allProducts, static function (array $product) use (
    $categorySlug,
    $searchQuery,
    $minPrice,
    $maxPrice,
    $brandFilters,
    $stockOnly,
    $ratingFilter
): bool {
    if ($categorySlug !== 'all' && $product['category_slug'] !== $categorySlug) {
        return false;
    }
    if ($searchQuery !== '' && mb_stripos($product['name'], $searchQuery, 0, 'UTF-8') === false) {
        return false;
    }
    if ((float) $product['price'] < $minPrice || (float) $product['price'] > $maxPrice) {
        return false;
    }
    if ($brandFilters !== [] && !in_array($product['brand'], $brandFilters, true)) {
        return false;
    }
    if ($stockOnly && (int) $product['in_stock'] !== 1) {
        return false;
    }
    if ($ratingFilter > 0 && (float) $product['rating'] < $ratingFilter) {
        return false;
    }

    return true;
}));

usort($products, static function (array $left, array $right) use ($sort): int {
    return match ($sort) {
        'price_asc' => $left['price'] <=> $right['price'],
        'price_desc' => $right['price'] <=> $left['price'],
        'rating_desc' => ($right['rating'] <=> $left['rating']) ?: ($right['reviews_count'] <=> $left['reviews_count']),
        'name_asc' => strnatcasecmp($left['name'], $right['name']),
        default => (($right['reviews_count'] * $right['rating']) <=> ($left['reviews_count'] * $left['rating'])),
    };
});

$flash = consumeCatalogFlash();
$selectedCategory = catalogCategory($categorySlug);
$sortLabels = [
    'popular' => 'По популярности',
    'price_asc' => 'Сначала дешёвые',
    'price_desc' => 'Сначала дорогие',
    'rating_desc' => 'По рейтингу',
    'name_asc' => 'По названию',
];

function catalogQuery(array $changes = []): string
{
    $query = $_GET;
    foreach ($changes as $key => $value) {
        if ($value === null || $value === '' || $value === [] || $value === 'all' && $key === 'category') {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
    }

    return http_build_query($query);
}

function catalogIcon(string $name): string
{
    $map = [
        'grid' => 'icon-grid',
        'cpu' => 'icon-cpu',
        'gpu' => 'icon-gpu',
        'motherboard' => 'icon-motherboard',
        'ram' => 'icon-ram',
        'ssd' => 'icon-ssd',
        'power' => 'icon-power',
        'cooling' => 'icon-cooling',
        'case' => 'icon-case',
        'headset' => 'icon-headset',
    ];
    return $map[$name] ?? 'icon-grid';
}

require __DIR__ . '/app/views/header.php';
?>

<main class="catalog-page">
    <svg class="svg-sprite" aria-hidden="true">
        <symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol>
        <symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol>
        <symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol>
        <symbol id="icon-chart" viewBox="0 0 24 24"><path d="M5 19V11M12 19V5M19 19v-8"></path></symbol>
        <symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol>
        <symbol id="icon-chevron" viewBox="0 0 24 24"><path d="m7 10 5 5 5-5"></path></symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="M4 12h15"></path><path d="m13 6 6 6-6 6"></path></symbol>
        <symbol id="icon-grid" viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"></rect><rect x="14" y="4" width="6" height="6" rx="1"></rect><rect x="4" y="14" width="6" height="6" rx="1"></rect><rect x="14" y="14" width="6" height="6" rx="1"></rect></symbol>
        <symbol id="icon-cpu" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="1"></rect><rect x="9" y="9" width="6" height="6"></rect><path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"></path></symbol>
        <symbol id="icon-gpu" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="11" rx="1"></rect><circle cx="9" cy="11.5" r="2.5"></circle><circle cx="16" cy="11.5" r="2.5"></circle><path d="M6 19v2M18 19v2M21 9h1v5h-1"></path></symbol>
        <symbol id="icon-motherboard" viewBox="0 0 24 24"><rect x="5" y="3" width="14" height="18" rx="1"></rect><rect x="8" y="6" width="5" height="5"></rect><path d="M8 15h8M8 18h3M16 6v5"></path></symbol>
        <symbol id="icon-ram" viewBox="0 0 24 24"><path d="M3 8h18v8H3zM6 8v8M10 8v8M14 8v8M18 8v8M6 5v3M10 5v3M14 5v3M18 5v3"></path></symbol>
        <symbol id="icon-ssd" viewBox="0 0 24 24"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M8 7h8M8 11h8M8 15h3M8 18h2"></path></symbol>
        <symbol id="icon-power" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="1"></rect><circle cx="8" cy="12" r="3"></circle><path d="M14 9h4M14 12h4M14 15h4"></path></symbol>
        <symbol id="icon-case" viewBox="0 0 24 24"><rect x="6" y="2" width="12" height="20" rx="1"></rect><circle cx="12" cy="6" r="1"></circle><path d="M9 10h6M9 14h6M9 18h3"></path></symbol>
        <symbol id="icon-cooling" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"></circle><circle cx="12" cy="12" r="2"></circle><path d="M12 10c-2-4 1-5 2-5M14 12c4-2 5 1 5 2M12 14c2 4-1 5-2 5M10 12c-4 2-5-1-5-2"></path></symbol>
        <symbol id="icon-headset" viewBox="0 0 24 24"><path d="M4 13v-1a8 8 0 0 1 16 0v1"></path><path d="M4 13h3v6H5a1 1 0 0 1-1-1zM20 13h-3v6h2a1 1 0 0 1 1-1zM17 19c0 1.5-2 2-4 2"></path></symbol>
        <symbol id="icon-truck" viewBox="0 0 24 24"><path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"></path><circle cx="7" cy="18" r="2"></circle><circle cx="18" cy="18" r="2"></circle></symbol>
        <symbol id="icon-shield" viewBox="0 0 24 24"><path d="M12 3 20 6v5c0 5-3.2 8.2-8 10-4.8-1.8-8-5-8-10V6l8-3Z"></path><path d="m8.5 12 2.2 2.2 4.8-5"></path></symbol>
        <symbol id="icon-refresh" viewBox="0 0 24 24"><path d="M20 11a8 8 0 0 0-14-4L4 9"></path><path d="M4 4v5h5M4 13a8 8 0 0 0 14 4l2-2M20 20v-5h-5"></path></symbol>
    </svg>

    <section class="catalog-hero">
        <div class="container catalog-hero__inner">
            <div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><span>Каталог</span></div>
            <h1>Каталог товаров</h1>
            <p>Широкий выбор комплектующих<br>для сборки идеального ПК</p>
            <div class="catalog-hero__visual"><img src="assets/images/5080.png" alt="Видеокарта"></div>
        </div>
    </section>

    <?php if ($flash): ?>
        <div class="container flash flash--<?= htmlspecialchars((string) ($flash['type'] ?? 'success'), ENT_QUOTES, 'UTF-8') ?>" role="status">
            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="container catalog-layout">
        <aside class="catalog-sidebar">
            <form action="catalog.php" method="get" class="catalog-filters">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort, ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($searchQuery !== ''): ?><input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
                <section class="filter-section filter-section--categories">
                    <h2>Категории</h2>
                    <div class="filter-category-list">
                        <?php foreach (catalogCategories() as $category): ?>
                            <a class="filter-category <?= $category['slug'] === $categorySlug ? 'is-active' : '' ?>" href="catalog.php?<?= htmlspecialchars(catalogQuery(['category' => $category['slug']]), ENT_QUOTES, 'UTF-8') ?>">
                                <svg class="filter-category__icon"><use href="#<?= catalogIcon($category['icon']) ?>"></use></svg>
                                <span><?= htmlspecialchars($category['short_title'], ENT_QUOTES, 'UTF-8') ?></span><b>›</b>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="filter-section">
                    <h2>Цена</h2>
                    <div class="range-track"><span></span><i></i><i></i></div>
                    <div class="price-inputs"><label>от <input type="number" name="min_price" value="<?= $minPrice ?>" min="0" max="1000000"></label><label>до <input type="number" name="max_price" value="<?= $maxPrice ?>" min="0" max="1000000"></label></div>
                </section>
                <section class="filter-section">
                    <h2>Бренд</h2>
                    <div class="check-list">
                        <?php foreach ($brands as $brand => $count): ?>
                            <label><input type="checkbox" name="brand[]" value="<?= htmlspecialchars((string) $brand, ENT_QUOTES, 'UTF-8') ?>" <?= in_array($brand, $brandFilters, true) ? 'checked' : '' ?>><span><?= htmlspecialchars((string) $brand, ENT_QUOTES, 'UTF-8') ?></span><small>(<?= $count ?>)</small></label>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="filter-section filter-section--compact"><label class="check-line"><input type="checkbox" name="in_stock" value="1" <?= $stockOnly ? 'checked' : '' ?>><span>В наличии</span><small>(<?= count(array_filter($allProducts, static fn (array $item): bool => (int) $item['in_stock'] === 1)) ?>)</small></label></section>
                <section class="filter-section">
                    <h2>Рейтинг</h2>
                    <div class="radio-list">
                        <?php foreach ([4.8 => 'и выше', 4.5 => 'и выше', 4.0 => 'и выше'] as $rating => $label): ?>
                            <label><input type="radio" name="rating" value="<?= $rating ?>" <?= $ratingFilter === (float) $rating ? 'checked' : '' ?>><span class="stars">★★★★★</span><small><?= $label ?></small></label>
                        <?php endforeach; ?>
                    </div>
                </section>
                <button type="submit" class="button filter-submit">Применить фильтры</button>
                <a href="catalog.php" class="button filter-reset"><svg class="icon icon--small"><use href="#icon-refresh"></use></svg> Сбросить фильтры</a>
            </form>
        </aside>

        <section class="catalog-results" aria-labelledby="catalog-results-title">
            <div class="catalog-toolbar">
                <p id="catalog-results-title">Найдено товаров: <strong><?= number_format(count($products), 0, ',', ' ') ?></strong><?php if ($selectedCategory && $selectedCategory['slug'] !== 'all'): ?><span class="toolbar-category"> · <?= htmlspecialchars($selectedCategory['title'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></p>
                <div class="catalog-toolbar__actions">
                    <label class="sort-control">Сортировка:
                        <select onchange="window.location.href=this.value">
                            <?php foreach ($sortLabels as $sortValue => $sortLabel): ?>
                                <option value="catalog.php?<?= htmlspecialchars(catalogQuery(['sort' => $sortValue]), ENT_QUOTES, 'UTF-8') ?>" <?= $sort === $sortValue ? 'selected' : '' ?>><?= htmlspecialchars($sortLabel, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="view-switcher" aria-label="Вид каталога"><button type="button" class="is-active" data-view="grid" aria-label="Сетка"><svg class="icon"><use href="#icon-grid"></use></svg></button><button type="button" data-view="list" aria-label="Список"><span>☷</span></button></div>
                </div>
            </div>

            <?php if ($products): ?>
                <div class="catalog-products" data-products-grid>
                    <?php foreach ($products as $product): ?>
                        <article class="catalog-product-card" data-product-card>
                            <div class="catalog-product-card__top"><span class="badge <?= htmlspecialchars($product['badge_class'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                                <form method="post" class="inline-action"><input type="hidden" name="store_action" value="toggle_favorite"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'catalog.php', ENT_QUOTES, 'UTF-8') ?>"><button class="favorite-button <?= isFavorite((string) $product['id']) ? 'is-active' : '' ?>" type="submit" aria-label="<?= isFavorite((string) $product['id']) ? 'Удалить из избранного' : 'Добавить в избранное' ?>"><svg class="icon"><use href="#icon-heart"></use></svg></button></form>
                            </div>
                            <a class="catalog-product-card__visual" href="product.php?id=<?= urlencode((string) $product['id']) ?>"><img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy"></a>
                            <h3><a href="product.php?id=<?= urlencode((string) $product['id']) ?>"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                            <div class="rating"><span>★</span> <?= number_format((float) $product['rating'], 1, ',', ' ') ?><small>(<?= number_format((int) $product['reviews_count'], 0, ',', ' ') ?> отзыв<?= ((int) $product['reviews_count'] % 10 === 1 && (int) $product['reviews_count'] % 100 !== 11) ? '' : 'ов' ?>)</small></div>
                            <div class="catalog-product-card__bottom"><strong><?= catalogPrice((float) $product['price']) ?></strong><form method="post" class="inline-action"><input type="hidden" name="store_action" value="add_cart"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $product['id'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'catalog.php', ENT_QUOTES, 'UTF-8') ?>"><button class="add-cart add-cart--icon" type="submit" aria-label="Добавить в корзину"><svg class="icon"><use href="#icon-cart"></use></svg><span>В корзину</span></button></form></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="catalog-empty"><h2>Ничего не найдено</h2><p>Попробуйте изменить фильтры или выбрать другую категорию.</p><a href="catalog.php" class="button button--primary">Показать все товары</a></div>
            <?php endif; ?>
        </section>
    </div>

    <section class="catalog-benefits container">
        <div class="catalog-benefit"><svg class="icon"><use href="#icon-truck"></use></svg><span><strong>Быстрая доставка</strong>от 1 дня</span></div>
        <div class="catalog-benefit"><svg class="icon"><use href="#icon-shield"></use></svg><span><strong>Гарантия</strong>от 1 года</span></div>
        <div class="catalog-benefit"><svg class="icon"><use href="#icon-refresh"></use></svg><span><strong>Лёгкий возврат</strong>в течение 14 дней</span></div>
        <div class="catalog-benefit"><svg class="icon"><use href="#icon-heart"></use></svg><span><strong>Поддержка</strong>24/7</span></div>
        <div class="catalog-benefit catalog-benefit--promo"><strong>Собери свой<br>идеальный ПК</strong><a href="index.php#popular" aria-label="Перейти к рекомендациям">→</a></div>
    </section>
</main>

<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>

<?php if ($authError): ?>
    <div class="catalog-auth-error visually-hidden" aria-hidden="true"><?= htmlspecialchars($authError, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
</body>
</html>
