<?php

declare(strict_types=1);

/**
 * Shared catalogue data and session-store helpers.
 *
 * The catalogue works with the original four products from the first
 * migration as well as the expanded demo data. Optional columns are read
 * only when they exist, so the page remains compatible with an older local
 * database until database/schema.sql is imported again.
 */

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

/** @return array<int, array<string, mixed>> */
function catalogDemoProducts(): array
{
    return [
        [
            'id' => 'demo-intel-i7-14700k',
            'name' => 'Процессор Intel Core i7-14700K',
            'rating' => 4.9,
            'reviews_count' => 842,
            'price' => 49990,
            'old_price' => null,
            'badge' => 'Хит',
            'badge_class' => 'badge--orange',
            'art' => 'cpu',
            'image_path' => 'assets/images/7800x3d.png',
            'category_slug' => 'processors',
            'brand' => 'Intel',
            'in_stock' => 1,
            'description' => 'Производительный процессор для игр, стриминга и рабочих задач.',
        ],
        [
            'id' => 'demo-ryzen-7-9700x',
            'name' => 'Процессор AMD Ryzen 7 9700X',
            'rating' => 4.9,
            'reviews_count' => 521,
            'price' => 44990,
            'old_price' => null,
            'badge' => 'Новинка',
            'badge_class' => 'badge--green',
            'art' => 'cpu',
            'image_path' => 'assets/images/7800x3d.png',
            'category_slug' => 'processors',
            'brand' => 'AMD',
            'in_stock' => 1,
            'description' => 'Современная восьмиядерная платформа для быстрой сборки нового поколения.',
        ],
        [
            'id' => 'demo-msi-b760-gaming',
            'name' => 'Материнская плата MSI B760 GAMING PLUS WIFI',
            'rating' => 4.7,
            'reviews_count' => 418,
            'price' => 22990,
            'old_price' => 25990,
            'badge' => 'Акция',
            'badge_class' => 'badge--red',
            'art' => 'motherboard',
            'image_path' => 'assets/images/b650 msi.png',
            'category_slug' => 'motherboards',
            'brand' => 'MSI',
            'in_stock' => 1,
            'description' => 'Надёжная плата с Wi-Fi для игровых систем на Intel.',
        ],
        [
            'id' => 'demo-asus-rtx-4070-super',
            'name' => 'Видеокарта ASUS GeForce RTX 4070 SUPER',
            'rating' => 4.8,
            'reviews_count' => 621,
            'price' => 79990,
            'old_price' => null,
            'badge' => 'Хит',
            'badge_class' => 'badge--orange',
            'art' => 'gpu',
            'image_path' => 'assets/images/5080.png',
            'category_slug' => 'gpu',
            'brand' => 'ASUS',
            'in_stock' => 1,
            'description' => 'Тихая видеокарта для игр в высоком разрешении и создания контента.',
        ],
        [
            'id' => 'demo-kingston-fury-32gb',
            'name' => 'Оперативная память Kingston FURY Beast 32GB',
            'rating' => 4.8,
            'reviews_count' => 379,
            'price' => 12990,
            'old_price' => null,
            'badge' => 'Популярное',
            'badge_class' => 'badge--blue',
            'art' => 'ram',
            'image_path' => 'assets/images/ram_homePage.png',
            'category_slug' => 'ram',
            'brand' => 'Kingston',
            'in_stock' => 1,
            'description' => 'Комплект быстрой памяти DDR5 с запасом для игр и многозадачности.',
        ],
        [
            'id' => 'demo-samsung-990-pro',
            'name' => 'SSD накопитель Samsung 990 PRO 1TB',
            'rating' => 4.9,
            'reviews_count' => 770,
            'price' => 16990,
            'old_price' => null,
            'badge' => 'Хит',
            'badge_class' => 'badge--orange',
            'art' => 'ssd',
            'image_path' => 'assets/images/ram_homePage.png',
            'category_slug' => 'storage',
            'brand' => 'Samsung',
            'in_stock' => 1,
            'description' => 'Быстрый NVMe-накопитель для системы, игр и больших проектов.',
        ],
        [
            'id' => 'demo-deepcool-px1000g',
            'name' => 'Блок питания DeepCool PX1000G 1000W',
            'rating' => 4.8,
            'reviews_count' => 208,
            'price' => 19990,
            'old_price' => null,
            'badge' => 'Хит',
            'badge_class' => 'badge--orange',
            'art' => 'power',
            'image_path' => 'assets/images/deepcool-1000.png',
            'category_slug' => 'power',
            'brand' => 'DeepCool',
            'in_stock' => 1,
            'description' => 'Мощный модульный блок питания с тихим охлаждением.',
        ],
        [
            'id' => 'demo-deepcool-ak620',
            'name' => 'Система охлаждения DeepCool AK620',
            'rating' => 4.7,
            'reviews_count' => 352,
            'price' => 8990,
            'old_price' => null,
            'badge' => 'Популярное',
            'badge_class' => 'badge--blue',
            'art' => 'cooling',
            'image_path' => 'assets/images/cpu-fan_homePage.png',
            'category_slug' => 'cooling',
            'brand' => 'DeepCool',
            'in_stock' => 1,
            'description' => 'Башенный кулер для стабильной и тихой работы производительного ПК.',
        ],
        [
            'id' => 'demo-cougar-mx600',
            'name' => 'Корпус Cougar MX600 RGB',
            'rating' => 4.8,
            'reviews_count' => 247,
            'price' => 11990,
            'old_price' => null,
            'badge' => 'Новинка',
            'badge_class' => 'badge--green',
            'art' => 'case',
            'image_path' => 'assets/images/hero-img.png',
            'category_slug' => 'cases',
            'brand' => 'Cougar',
            'in_stock' => 1,
            'description' => 'Просторный корпус с хорошей продуваемостью и подсветкой.',
        ],
        [
            'id' => 'demo-amd-7800x3d',
            'name' => 'AMD Ryzen 7 7800X3D',
            'rating' => 4.9,
            'reviews_count' => 1234,
            'price' => 37990,
            'old_price' => null,
            'badge' => 'Хит продаж',
            'badge_class' => 'badge--orange',
            'art' => 'cpu',
            'image_path' => 'assets/images/7800x3d.png',
            'category_slug' => 'processors',
            'brand' => 'AMD',
            'in_stock' => 1,
            'description' => 'Игровой процессор с большим 3D-кэшем для высокой частоты кадров.',
        ],
        [
            'id' => 'demo-rtx-4080-super',
            'name' => 'GeForce RTX 4080 SUPER',
            'rating' => 4.9,
            'reviews_count' => 856,
            'price' => 129990,
            'old_price' => null,
            'badge' => 'Новинка',
            'badge_class' => 'badge--green',
            'art' => 'gpu',
            'image_path' => 'assets/images/5080.png',
            'category_slug' => 'gpu',
            'brand' => 'NVIDIA',
            'in_stock' => 1,
            'description' => 'Флагманская видеокарта для современных игр и профессиональной графики.',
        ],
        [
            'id' => 'demo-msi-b650-tomahawk',
            'name' => 'MSI MAG B650 TOMAHAWK WIFI',
            'rating' => 4.8,
            'reviews_count' => 743,
            'price' => 19990,
            'old_price' => 22990,
            'badge' => '-12%',
            'badge_class' => 'badge--red',
            'art' => 'motherboard',
            'image_path' => 'assets/images/b650 msi.png',
            'category_slug' => 'motherboards',
            'brand' => 'MSI',
            'in_stock' => 1,
            'description' => 'Материнская плата на чипсете B650 для сбалансированной сборки AMD.',
        ],
    ];
}

function catalogInferMetadata(string $name): array
{
    $value = mb_strtolower($name, 'UTF-8');

    $category = 'peripherals';
    if (preg_match('/ryzen|core i|xeon|процессор/', $value)) {
        $category = 'processors';
    } elseif (preg_match('/rtx|gtx|radeon|geforce|видеокарт/', $value)) {
        $category = 'gpu';
    } elseif (preg_match('/b650|b760|z790|tomahawk|материнск/', $value)) {
        $category = 'motherboards';
    } elseif (preg_match('/ram|fury|ddr|оперативн|памят/', $value)) {
        $category = 'ram';
    } elseif (preg_match('/ssd|990 pro|nvme|накопител|hdd/', $value)) {
        $category = 'storage';
    } elseif (preg_match('/px1000|блок питан|power supply/', $value)) {
        $category = 'power';
    } elseif (preg_match('/ak620|cool|охлажд/', $value)) {
        $category = 'cooling';
    } elseif (preg_match('/cougar|корпус|case/', $value)) {
        $category = 'cases';
    }

    $brand = 'Другой бренд';
    foreach (['AMD', 'Intel', 'NVIDIA', 'ASUS', 'MSI', 'Kingston', 'Samsung', 'DeepCool', 'Cougar', 'Gigabyte'] as $knownBrand) {
        if (mb_stripos($name, $knownBrand, 0, 'UTF-8') !== false) {
            $brand = $knownBrand;
            break;
        }
    }

    return ['category_slug' => $category, 'brand' => $brand, 'in_stock' => 1];
}

function catalogNormalizeProduct(array $row): array
{
    $metadata = catalogInferMetadata((string) ($row['name'] ?? 'Товар'));
    $category = (string) ($row['category_slug'] ?? $metadata['category_slug']);
    if (!catalogCategory($category) || $category === 'all') {
        $category = $metadata['category_slug'];
    }

    return [
        'id' => (string) ($row['id'] ?? ''),
        'name' => (string) ($row['name'] ?? 'Товар'),
        'rating' => (float) ($row['rating'] ?? 0),
        'reviews_count' => (int) ($row['reviews_count'] ?? 0),
        'price' => (float) ($row['price'] ?? 0),
        'old_price' => $row['old_price'] === null || $row['old_price'] === '' ? null : (float) $row['old_price'],
        'badge' => (string) ($row['badge'] ?? ''),
        'badge_class' => (string) ($row['badge_class'] ?? 'badge--orange'),
        'art' => (string) ($row['art'] ?? 'product'),
        'image_path' => (string) ($row['image_path'] ?? 'assets/images/hero-img.png'),
        'category_slug' => $category,
        'brand' => (string) ($row['brand'] ?? $metadata['brand']),
        'in_stock' => (int) ($row['in_stock'] ?? $metadata['in_stock']) === 1 ? 1 : 0,
        'description' => (string) ($row['description'] ?? 'Комплектующее для производительной и надёжной сборки ПК.'),
    ];
}

/** @return array<int, array<string, mixed>> */
function catalogProducts(PDO $pdo): array
{
    static $cache = [];
    $cacheKey = spl_object_id($pdo);
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $products = [];
    try {
        $columns = $pdo->query('SHOW COLUMNS FROM products')->fetchAll(PDO::FETCH_COLUMN);
        $select = ['id', 'name', 'rating', 'reviews_count', 'price', 'old_price', 'badge', 'badge_class', 'art', 'image_path', 'is_active'];
        foreach (['category_slug', 'brand', 'in_stock', 'description'] as $optionalColumn) {
            if (in_array($optionalColumn, $columns, true)) {
                $select[] = $optionalColumn;
            }
        }

        $statement = $pdo->query(
            'SELECT ' . implode(', ', array_map(static fn (string $column): string => '`' . $column . '`', $select)) .
            ' FROM `products` WHERE `is_active` = 1 ORDER BY `is_featured` DESC, `id` ASC'
        );
        foreach ($statement->fetchAll() as $row) {
            $products[] = catalogNormalizeProduct($row);
        }
    } catch (PDOException $exception) {
        // The page can still render the demo catalogue if the local database
        // has not been imported yet. The database error is not shown to users.
    }

    $names = [];
    foreach ($products as $product) {
        $names[mb_strtolower($product['name'], 'UTF-8')] = true;
    }
    foreach (catalogDemoProducts() as $demoProduct) {
        $nameKey = mb_strtolower($demoProduct['name'], 'UTF-8');
        if (!isset($names[$nameKey])) {
            $products[] = $demoProduct;
        }
    }

    return $cache[$cacheKey] = $products;
}

function catalogProduct(PDO $pdo, string $id): ?array
{
    foreach (catalogProducts($pdo) as $product) {
        if ((string) $product['id'] === $id) {
            return $product;
        }
    }

    return null;
}

function catalogPrice(float $price): string
{
    return number_format($price, 0, ',', ' ') . ' ₽';
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
