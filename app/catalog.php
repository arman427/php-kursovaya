<?php

declare(strict_types=1);

const CATALOG_PLACEHOLDER_IMAGE = 'assets/images/product-placeholder.svg';

function catalogCategories(): array
{
    return [
        ['slug' => 'all', 'title' => 'Все категории', 'short_title' => 'Все товары', 'icon' => 'grid'],
        ['slug' => 'processors', 'title' => 'Процессоры', 'short_title' => 'Процессоры', 'icon' => 'cpu'],
        ['slug' => 'gpu', 'title' => 'Видеокарты', 'short_title' => 'Видеокарты', 'icon' => 'gpu'],
        ['slug' => 'motherboards', 'title' => 'Материнские платы', 'short_title' => 'Материнские платы', 'icon' => 'motherboard'],
        ['slug' => 'ram', 'title' => 'Оперативная память', 'short_title' => 'Оперативная память', 'icon' => 'ram'],
        ['slug' => 'storage', 'title' => 'Накопители', 'short_title' => 'Накопители', 'icon' => 'ssd'],
        ['slug' => 'power', 'title' => 'Блоки питания', 'short_title' => 'Блоки питания', 'icon' => 'power'],
        ['slug' => 'cooling', 'title' => 'Системы охлаждения', 'short_title' => 'Охлаждение', 'icon' => 'cooling'],
        ['slug' => 'cases', 'title' => 'Корпуса', 'short_title' => 'Корпуса', 'icon' => 'case'],
        ['slug' => 'peripherals', 'title' => 'Периферия', 'short_title' => 'Периферия', 'icon' => 'headset'],
    ];
}

function catalogCategory(string $slug): ?array
{
    foreach (catalogCategories() as $category) {
        if ($category['slug'] === $slug) {
            return $category;
        }
    }

    return null;
}

function catalogImagePath(string $path): string
{
    $path = trim($path);
    if ($path === '' || !is_file(__DIR__ . '/../' . $path)) {
        return CATALOG_PLACEHOLDER_IMAGE;
    }

    return $path;
}

function catalogNormalizeProduct(array $row): array
{
    $category = (string) $row['category_slug'];
    if ($category === 'all' || catalogCategory($category) === null) {
        $category = 'peripherals';
    }

    return [
        'id' => (string) $row['id'],
        'name' => (string) $row['name'],
        'rating' => (float) $row['rating'],
        'reviews_count' => (int) $row['reviews_count'],
        'price' => (float) $row['price'],
        'old_price' => $row['old_price'] === null || $row['old_price'] === '' ? null : (float) $row['old_price'],
        'badge' => (string) $row['badge'],
        'badge_class' => (string) $row['badge_class'],
        'art' => (string) $row['art'],
        'image_path' => catalogImagePath((string) $row['image_path']),
        'category_slug' => $category,
        'brand' => (string) $row['brand'],
        'in_stock' => (int) $row['in_stock'] === 1 ? 1 : 0,
        'description' => (string) ($row['description'] ?? ''),
    ];
}

function catalogProducts(PDO $pdo): array
{
    static $cache = [];
    $cacheKey = spl_object_id($pdo);
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $statement = $pdo->query(
        'SELECT id, name, rating, reviews_count, price, old_price, badge, badge_class, art, image_path,
                category_slug, brand, in_stock, description
         FROM products
         WHERE is_active = 1
         ORDER BY is_featured DESC, id ASC'
    );

    $products = [];
    foreach ($statement->fetchAll() as $row) {
        $products[] = catalogNormalizeProduct($row);
    }

    return $cache[$cacheKey] = $products;
}

function catalogProduct(PDO $pdo, string $id): ?array
{
    foreach (catalogProducts($pdo) as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }

    return null;
}

function catalogPrice(float $price): string
{
    return number_format($price, 0, ',', ' ') . ' ₽';
}

function catalogRequireAuth(): bool
{
    if (isAuthenticated()) {
        return true;
    }

    $_SESSION['auth_notice'] = 'Войдите или зарегистрируйтесь, чтобы добавлять товары в корзину и избранное.';
    return false;
}

function consumeAuthNotice(): ?string
{
    $notice = $_SESSION['auth_notice'] ?? null;
    unset($_SESSION['auth_notice']);

    return is_string($notice) && $notice !== '' ? $notice : null;
}

function catalogSafeReturnTo(string $target, string $fallback = 'catalog.php'): string
{
    if ($target === '' || str_starts_with($target, '/') || str_contains($target, '://') || str_contains($target, "\r") || str_contains($target, "\n")) {
        return $fallback;
    }

    return $target;
}

function catalogRedirectBack(string $fallback = 'catalog.php'): never
{
    $target = catalogSafeReturnTo((string) ($_POST['return_to'] ?? $fallback), $fallback);
    header('Location: ' . $target);
    exit;
}

function catalogFlash(string $message, string $type = 'success'): void
{
    $_SESSION['store_flash'] = ['message' => $message, 'type' => $type];
}

function consumeCatalogFlash(): ?array
{
    $flash = $_SESSION['store_flash'] ?? null;
    unset($_SESSION['store_flash']);

    return is_array($flash) ? $flash : null;
}

function catalogIsAjax(): bool
{
    return strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
}

function catalogAjaxResponse(?string $message = null, string $type = 'success'): never
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => $type !== 'error',
        'message' => $message ?? '',
        'type' => $type,
        'cartCount' => cartItemCount(),
        'favoriteCount' => count(favoriteIds()),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function cartItems(): array
{
    $cart = $_SESSION['cart'] ?? [];
    if (!is_array($cart)) {
        return [];
    }

    $items = [];
    foreach ($cart as $id => $quantity) {
        $quantity = (int) $quantity;
        if ($quantity > 0) {
            $items[(string) $id] = min($quantity, 99);
        }
    }

    return $items;
}

function cartItemCount(): int
{
    return array_sum(cartItems());
}

function addCartItem(string $id, int $quantity = 1): void
{
    $items = cartItems();
    $items[$id] = min(($items[$id] ?? 0) + max(1, $quantity), 99);
    $_SESSION['cart'] = $items;
}

function setCartItemQuantity(string $id, int $quantity): void
{
    $items = cartItems();
    if ($quantity <= 0) {
        unset($items[$id]);
    } else {
        $items[$id] = min($quantity, 99);
    }
    $_SESSION['cart'] = $items;
}

function removeCartItem(string $id): void
{
    $items = cartItems();
    unset($items[$id]);
    $_SESSION['cart'] = $items;
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function favoriteIds(): array
{
    $favorites = $_SESSION['favorites'] ?? [];
    if (!is_array($favorites)) {
        return [];
    }

    return array_values(array_unique(array_map(static fn ($id): string => (string) $id, $favorites)));
}

function isFavorite(string $id): bool
{
    return in_array($id, favoriteIds(), true);
}

function toggleFavorite(string $id): bool
{
    $favorites = favoriteIds();
    $index = array_search($id, $favorites, true);
    if ($index === false) {
        $favorites[] = $id;
        $_SESSION['favorites'] = $favorites;
        return true;
    }

    unset($favorites[$index]);
    $_SESSION['favorites'] = array_values($favorites);
    return false;
}

function removeFavorite(string $id): void
{
    $_SESSION['favorites'] = array_values(array_filter(
        favoriteIds(),
        static fn (string $favoriteId): bool => $favoriteId !== $id
    ));
}

function cartTotal(PDO $pdo): float
{
    $total = 0.0;
    foreach (cartItems() as $id => $quantity) {
        $product = catalogProduct($pdo, $id);
        if ($product !== null) {
            $total += (float) $product['price'] * $quantity;
        }
    }

    return $total;
}
