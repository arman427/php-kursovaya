<?php
$pageTitle = $pageTitle ?? 'PC Builder';
$isAuthenticated = isAuthenticated();
$siteConfig = require __DIR__ . '/../config/site.php';
$pageDescription = mb_strimwidth(trim((string) ($pageDescription ?? $siteConfig['default_description'])), 0, 200, '…', 'UTF-8');
$pageImage = (string) ($pageImage ?? $siteConfig['default_image']);
$metaScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$metaHost = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
$metaBaseDir = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
$metaUrl = $metaScheme . '://' . $metaHost . (string) ($_SERVER['REQUEST_URI'] ?? '/');
$metaImage = preg_match('#^https?://#', $pageImage) === 1
   ? $pageImage
   : $metaScheme . '://' . $metaHost . $metaBaseDir . '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($pageImage, '/'))));
$metrikaId = (int) $siteConfig['metrika_id'];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
   <meta property="og:type" content="website">
   <meta property="og:site_name" content="<?= htmlspecialchars($siteConfig['site_name'], ENT_QUOTES, 'UTF-8') ?>">
   <meta property="og:locale" content="ru_RU">
   <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
   <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
   <meta property="og:url" content="<?= htmlspecialchars($metaUrl, ENT_QUOTES, 'UTF-8') ?>">
   <meta property="og:image" content="<?= htmlspecialchars($metaImage, ENT_QUOTES, 'UTF-8') ?>">
   <meta name="twitter:card" content="summary">
   <?php if ($siteConfig['twitter_site'] !== ''): ?><meta name="twitter:site" content="<?= htmlspecialchars($siteConfig['twitter_site'], ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
   <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
   <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
   <meta name="twitter:image" content="<?= htmlspecialchars($metaImage, ENT_QUOTES, 'UTF-8') ?>">
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/fonts.css">
   <link rel="stylesheet" href="assets/css/style.css?v=2">
   <script src="assets/js/main.js?v=2" defer></script>
   <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
   <?php if ($metrikaId > 0): ?>
      <script>
         (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
         m[i].l=1*new Date();
         for (var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}
         k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
         (window,document,"script","https://mc.yandex.ru/metrika/tag.js","ym");
         ym(<?= $metrikaId ?>,"init",{clickmap:true,trackLinks:true,accurateTrackBounce:true});
      </script>
   <?php endif; ?>
</head>

<body>
   <?php if ($metrikaId > 0): ?><noscript><div><img src="https://mc.yandex.ru/watch/<?= $metrikaId ?>" style="position:absolute;left:-9999px;" alt=""></div></noscript><?php endif; ?>
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
   <header class="site-header">
      <div class="container site-header__inner">
         <a href="index.php" class="brand" aria-label="PC Builder — на главную"><img
               src="<?= htmlspecialchars($logoMedia['image_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8') ?>"
               alt="<?= htmlspecialchars($logoMedia['alt_text'] ?? 'PC Builder', ENT_QUOTES, 'UTF-8') ?>" class="brand__mark"><span
               class="brand__name">PC BUILDER</span></a>
         <button type="button" class="mobile-menu-toggle" data-mobile-menu-toggle aria-expanded="false" aria-controls="site-navigation">
            <span></span><span></span><span></span><b class="visually-hidden">Открыть меню</b>
         </button>
         <nav class="site-nav" id="site-navigation" aria-label="Основная навигация">
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
         <div class="header-actions">
            <a href="favorites.php" class="header-action" aria-label="Избранное">
               <span class="header-action__visual"><svg class="icon">
                     <use href="#icon-heart"></use>
                  </svg><?php $favoriteCount = $isAuthenticated && is_array($_SESSION['favorites'] ?? null) ? count(array_unique($_SESSION['favorites'])) : 0; ?><?php if ($favoriteCount > 0): ?><b><?= $favoriteCount ?></b><?php endif; ?></span>
               <span class="header-action__label">Избранное</span>
            </a>
            <a href="cart.php" class="header-action header-action--cart" aria-label="Корзина">
               <span class="header-action__visual"><svg class="icon">
                     <use href="#icon-cart"></use>
                  </svg><?php $cartCount = $isAuthenticated && is_array($_SESSION['cart'] ?? null) ? array_sum(array_map('intval', $_SESSION['cart'])) : 0; ?><b><?= $cartCount ?></b></span>
               <span class="header-action__label">Корзина</span>
            </a>
         </div>
         <?php if ($isAuthenticated): ?>
            <div class="profile-menu" data-profile-menu>
               <button type="button" class="header-action header-action--profile" data-profile-toggle aria-label="Открыть профиль" aria-expanded="false" aria-controls="profile-dropdown">
                  <span class="header-action__visual"><svg class="icon">
                        <use href="#icon-user"></use>
                     </svg></span>
                  <span class="header-action__label">Профиль</span>
               </button>
               <div class="profile-dropdown" id="profile-dropdown" data-profile-dropdown role="menu" aria-hidden="true">
                  <div class="profile-dropdown__user"><span>Вы вошли как</span><strong><?= htmlspecialchars((string) $_SESSION['user_email'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                  <a class="profile-dropdown__link" href="profile.php" role="menuitem">Личный кабинет</a>
                  <a class="profile-dropdown__link" href="settings.php" role="menuitem">Настройки</a>
                  <a class="profile-dropdown__link" href="contacts.php" role="menuitem">Поддержка</a>
                  <form class="profile-dropdown__logout" method="post"><input type="hidden" name="auth_action" value="logout"><button type="submit" role="menuitem">Выйти</button></form>
               </div>
            </div>
         <?php else: ?>
            <button type="button" class="header-login" data-auth-open>Войти</button>
         <?php endif; ?>
      </div>
   </header>
   <?php require __DIR__ . '/auth-modal.php'; ?>