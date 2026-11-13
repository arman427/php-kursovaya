<?php
$pageTitle = $pageTitle ?? 'PC Builder';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <script src="assets/js/main.js?v=2" defer></script>
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
</head>

<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a href="index.php" class="brand" aria-label="PC Builder — на главную"><img
                    src="<?= htmlspecialchars($logoMedia['image_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($logoMedia['alt_text'] ?? 'PC Builder', ENT_QUOTES, 'UTF-8') ?>" class="brand__mark"><span
                    class="brand__name">PC BUILDER</span></a>
            <nav class="site-nav" aria-label="Основная навигация">
                <a href="catalog.php" class="site-nav__link">Каталог</a>
                <a href="index.php#popular" class="site-nav__link">Рекомендации</a>
                <a href="index.php#promotions" class="site-nav__link">Акции</a>
                <a href="index.php#benefits" class="site-nav__link">Как купить</a>
                <a href="contacts.php" class="site-nav__link">Контакты</a>
            </nav>
            <form class="search-form" action="search.php" method="get"><label class="visually-hidden"
                    for="header-search">Поиск товаров</label><input id="header-search" type="search" name="q"
                    placeholder="Поиск товаров..." value="<?= htmlspecialchars((string) ($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><button type="submit" aria-label="Найти"><svg class="icon">
                        <use href="#icon-search"></use>
                    </svg></button></form>
            <div class="header-actions"><a href="favorites.php" class="header-action" aria-label="Избранное"><svg
                        class="icon">
                        <use href="#icon-heart"></use>
                    </svg><?php $favoriteCount = is_array($_SESSION['favorites'] ?? null) ? count(array_unique($_SESSION['favorites'])) : 0; ?><?php if ($favoriteCount > 0): ?><b><?= $favoriteCount ?></b><?php endif; ?></a><a href="cart.php" class="header-action header-action--cart" aria-label="Корзина"><svg
                        class="icon">
                        <use href="#icon-cart"></use>
                    </svg><?php $cartCount = is_array($_SESSION['cart'] ?? null) ? array_sum(array_map('intval', $_SESSION['cart'])) : 0; ?><b><?= $cartCount ?></b></a></div>
            <?php if (!empty($_SESSION['user_email'])): ?>
                <div class="profile-menu" data-profile-menu>
                    <button type="button" class="header-action header-action--profile" data-profile-toggle
                        aria-label="Профиль пользователя" aria-expanded="false" aria-controls="profile-dropdown">
                        <svg class="icon">
                            <use href="#icon-user"></use>
                        </svg>
                    </button>
                    <div class="profile-dropdown" id="profile-dropdown" data-profile-dropdown role="menu"
                        aria-hidden="true">
                        <div class="profile-dropdown__user">
                            <span>Вы вошли как</span>
                            <strong><?= htmlspecialchars((string) $_SESSION['user_email'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                        <a class="profile-dropdown__link" href="profile.php" role="menuitem">Личный кабинет</a>
                        <form class="profile-dropdown__logout" method="post">
                            <input type="hidden" name="auth_action" value="logout">
                            <button type="submit" role="menuitem">Выйти</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <button type="button" class="header-login" data-auth-open>Войти</button>
            <?php endif; ?>
        </div>
    </header>
<?php require __DIR__ . '/auth-modal.php'; ?>