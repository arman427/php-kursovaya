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
    <link rel="stylesheet" href="assets/css/style.css">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a href="index.php" class="brand" aria-label="PC Builder — на главную">
                <img src="assets/images/logo.png" alt="" class="brand__mark">
                <span class="brand__name">PC BUILDER</span>
            </a>

            <nav class="site-nav" aria-label="Основная навигация">
                <a href="catalog.php" class="site-nav__link site-nav__link--active">Каталог <svg class="icon icon--chevron"><use href="#icon-chevron"></use></svg></a>
                <a href="#popular" class="site-nav__link">Рекомендации</a>
                <a href="#promotions" class="site-nav__link">Акции</a>
                <a href="#benefits" class="site-nav__link">Как купить <svg class="icon icon--chevron"><use href="#icon-chevron"></use></svg></a>
                <a href="contacts.php" class="site-nav__link">Контакты</a>
            </nav>

            <form class="search-form" action="search.php" method="get">
                <label class="visually-hidden" for="header-search">Поиск товаров</label>
                <input id="header-search" type="search" name="q" placeholder="Поиск товаров...">
                <button type="submit" aria-label="Найти"><svg class="icon"><use href="#icon-search"></use></svg></button>
            </form>

            <div class="header-actions">
                <a href="#" class="header-action" aria-label="Статистика"><svg class="icon"><use href="#icon-chart"></use></svg></a>
                <a href="favorites.php" class="header-action" aria-label="Избранное"><svg class="icon"><use href="#icon-heart"></use></svg></a>
                <a href="#" class="header-action" aria-label="Профиль"><svg class="icon"><use href="#icon-user"></use></svg></a>
                <a href="cart.php" class="header-action header-action--cart" aria-label="Корзина"><svg class="icon"><use href="#icon-cart"></use></svg><b>0</b></a>
            </div>
        </div>
    </header>
