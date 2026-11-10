<?php
$pageTitle = 'PC Builder — интернет-магазин комплектующих для ПК';
require __DIR__ . '/app/views/header.php';

$categories = [
   ['title' => 'Процессоры', 'icon' => 'cpu'],
   ['title' => 'Видеокарты', 'icon' => 'gpu'],
   ['title' => 'Материнские платы', 'icon' => 'motherboard'],
   ['title' => 'Оперативная память', 'icon' => 'ram'],
   ['title' => 'SSD и HDD', 'icon' => 'ssd'],
   ['title' => 'Блоки питания', 'icon' => 'power'],
   ['title' => 'Корпуса', 'icon' => 'case'],
   ['title' => 'Системы охлаждения', 'icon' => 'cooling'],
];

$products = [
   [
      'name' => 'AMD Ryzen 7 7800X3D',
      'rating' => '4.9',
      'reviews' => '1 234',
      'price' => '37 990 ₽',
      'badge' => 'Хит продаж',
      'badgeClass' => 'badge--orange',
      'art' => 'cpu',
   ],
   [
      'name' => 'GeForce RTX 4080 SUPER',
      'rating' => '4.9',
      'reviews' => '856',
      'price' => '129 990 ₽',
      'badge' => 'Новинка',
      'badgeClass' => 'badge--green',
      'art' => 'gpu',
   ],
   [
      'name' => 'MSI MAG B650 TOMAHAWK WIFI',
      'rating' => '4.8',
      'reviews' => '743',
      'price' => '19 990 ₽',
      'oldPrice' => '22 990 ₽',
      'badge' => '-12%',
      'badgeClass' => 'badge--red',
      'art' => 'motherboard',
   ],
   [
      'name' => 'DeepCool PX1000G',
      'rating' => '4.9',
      'reviews' => '420',
      'price' => '14 990 ₽',
      'badge' => 'Хит продаж',
      'badgeClass' => 'badge--orange',
      'art' => 'power',
   ],
];
?>

<main>
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
      <symbol id="icon-chevron" viewBox="0 0 24 24">
         <path d="m7 10 5 5 5-5"></path>
      </symbol>
      <symbol id="icon-arrow" viewBox="0 0 24 24">
         <path d="M4 12h15"></path>
         <path d="m13 6 6 6-6 6"></path>
      </symbol>
      <symbol id="icon-chevron-right" viewBox="0 0 24 24">
         <path d="m9 5 7 7-7 7"></path>
      </symbol>
      <symbol id="icon-chart" viewBox="0 0 24 24">
         <path d="M5 19V11M12 19V5M19 19v-8"></path>
      </symbol>
      <symbol id="icon-cpu" viewBox="0 0 24 24">
         <rect x="6" y="6" width="12" height="12" rx="1"></rect>
         <rect x="9" y="9" width="6" height="6"></rect>
         <path d="M9 2v4M15 2v4M9 18v4M15 18v4M2 9h4M2 15h4M18 9h4M18 15h4"></path>
      </symbol>
      <symbol id="icon-gpu" viewBox="0 0 24 24">
         <rect x="3" y="6" width="18" height="11" rx="1"></rect>
         <circle cx="9" cy="11.5" r="2.5"></circle>
         <circle cx="16" cy="11.5" r="2.5"></circle>
         <path d="M6 19v2M18 19v2M21 9h1v5h-1"></path>
      </symbol>
      <symbol id="icon-motherboard" viewBox="0 0 24 24">
         <rect x="5" y="3" width="14" height="18" rx="1"></rect>
         <rect x="8" y="6" width="5" height="5"></rect>
         <path d="M8 15h8M8 18h3M16 6v5"></path>
      </symbol>
      <symbol id="icon-ram" viewBox="0 0 24 24">
         <path d="M3 8h18v8H3zM6 8v8M10 8v8M14 8v8M18 8v8M6 5v3M10 5v3M14 5v3M18 5v3"></path>
      </symbol>
      <symbol id="icon-ssd" viewBox="0 0 24 24">
         <rect x="5" y="3" width="14" height="18" rx="2"></rect>
         <path d="M8 7h8M8 11h8M8 15h3M8 18h2"></path>
      </symbol>
      <symbol id="icon-power" viewBox="0 0 24 24">
         <rect x="3" y="5" width="18" height="14" rx="1"></rect>
         <circle cx="8" cy="12" r="3"></circle>
         <path d="M14 9h4M14 12h4M14 15h4"></path>
      </symbol>
      <symbol id="icon-case" viewBox="0 0 24 24">
         <rect x="6" y="2" width="12" height="20" rx="1"></rect>
         <circle cx="12" cy="6" r="1"></circle>
         <path d="M9 10h6M9 14h6M9 18h3"></path>
      </symbol>
      <symbol id="icon-cooling" viewBox="0 0 24 24">
         <circle cx="12" cy="12" r="8"></circle>
         <circle cx="12" cy="12" r="2"></circle>
         <path d="M12 10c-2-4 1-5 2-5M14 12c4-2 5 1 5 2M12 14c2 4-1 5-2 5M10 12c-4 2-5-1-5-2"></path>
      </symbol>
      <symbol id="icon-truck" viewBox="0 0 24 24">
         <path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"></path>
         <circle cx="7" cy="18" r="2"></circle>
         <circle cx="18" cy="18" r="2"></circle>
      </symbol>
      <symbol id="icon-shield" viewBox="0 0 24 24">
         <path d="M12 3 20 6v5c0 5-3.2 8.2-8 10-4.8-1.8-8-5-8-10V6l8-3Z"></path>
         <path d="m8.5 12 2.2 2.2 4.8-5"></path>
      </symbol>
      <symbol id="icon-headset" viewBox="0 0 24 24">
         <path d="M4 13v-1a8 8 0 0 1 16 0v1"></path>
         <path d="M4 13h3v6H5a1 1 0 0 1-1-1zM20 13h-3v6h2a1 1 0 0 0 1-1zM17 19c0 1.5-2 2-4 2"></path>
      </symbol>
      <symbol id="icon-refresh" viewBox="0 0 24 24">
         <path d="M20 11a8 8 0 0 0-14-4L4 9"></path>
         <path d="M4 4v5h5M4 13a8 8 0 0 0 14 4l2-2"></path>
         <path d="M20 20v-5h-5"></path>
      </symbol>
   </svg>

   <section class="hero container">
      <div class="hero__inner">
         <div class="hero__content">
            <p class="eyebrow"><span></span> Собери свой ПК</p>
            <h1>Get Your<br>Perfect<br>PC Today!</h1>
            <p class="hero__description">Мощные комплектующие для игр,<br>работы и творчества</p>
            <div class="hero__actions">
               <a href="catalog.php" class="button button--primary">Перейти в каталог <svg class="icon icon--small">
                     <use href="#icon-arrow"></use>
                  </svg></a>
               <a href="#popular" class="button button--outline">Узнать больше</a>
            </div>
            <div class="hero__slider">
               <strong>01</strong><span>/ 03</span>
               <button class="slider-button" type="button" aria-label="Предыдущий слайд">‹</button>
               <button class="slider-button" type="button" aria-label="Следующий слайд">›</button>
            </div>
         </div>

         <div class="hero__visual" aria-label="Изображение игрового компьютера">
            <div class="hero-placeholder">
               <span>Изображение ПК</span>
            </div>
         </div>
      </div>
      <div class="hero__scroll"><span>↓</span><small>Листайте<br>вниз</small></div>
   </section>

   <section class="category-bar container" aria-label="Категории товаров">
      <?php foreach ($categories as $category): ?>
         <a href="catalog.php" class="category-item">
            <svg class="category-item__icon">
               <use href="#icon-<?= $category['icon'] ?>"></use>
            </svg>
            <span><?= htmlspecialchars($category['title']) ?></span>
         </a>
      <?php endforeach; ?>
   </section>

   <section class="section container" id="popular">
      <div class="section-heading">
         <h2>Популярные товары</h2>
         <a href="catalog.php" class="text-link">Все товары <svg class="icon icon--small">
               <use href="#icon-arrow"></use>
            </svg></a>
      </div>

      <div class="products-grid">
         <?php foreach ($products as $product): ?>
            <article class="product-card">
               <div class="product-card__top">
                  <span class="badge <?= $product['badgeClass'] ?>"><?= htmlspecialchars($product['badge']) ?></span>
                  <button class="favorite-button" type="button" aria-label="Добавить в избранное"><svg class="icon">
                        <use href="#icon-heart"></use>
                     </svg></button>
               </div>
               <div class="product-visual product-visual--<?= $product['art'] ?>">
                  <?php if ($product['art'] === 'cpu'): ?>
                     <div class="cpu-box"><span>AMD</span><strong>RYZEN<br><b>7</b></strong><small>7000 SERIES</small></div>
                  <?php elseif ($product['art'] === 'gpu'): ?>
                     <div class="gpu-art"><span>GEFORCE<br><b>RTX 4080</b></span><i></i><i></i><i></i></div>
                  <?php elseif ($product['art'] === 'motherboard'): ?>
                     <div class="board-art"><i class="board-chip"></i><i class="board-slot"></i><i class="board-line board-line--one"></i><i class="board-line board-line--two"></i></div>
                  <?php else: ?>
                     <div class="power-art"><b>DEEPCOOL</b><span>PX1000G</span><i></i></div>
                  <?php endif; ?>
               </div>
               <h3><?= htmlspecialchars($product['name']) ?></h3>
               <div class="rating"><span>★</span> <?= htmlspecialchars($product['rating']) ?> <small>(<?= htmlspecialchars($product['reviews']) ?>)</small></div>
               <div class="product-card__bottom">
                  <div>
                     <?php if (!empty($product['oldPrice'])): ?><del><?= htmlspecialchars($product['oldPrice']) ?></del><?php endif; ?>
                     <strong><?= htmlspecialchars($product['price']) ?></strong>
                  </div>
                  <button class="add-cart" type="button" aria-label="Добавить в корзину"><svg class="icon">
                        <use href="#icon-cart"></use>
                     </svg><span>В корзину</span></button>
               </div>
            </article>
         <?php endforeach; ?>
      </div>
   </section>

   <section class="promos container">
      <a href="catalog.php" class="promo promo--cooling">
         <div class="promo__content">
            <p class="eyebrow"><span></span> Скидки до 30%</p>
            <h3>Охлаждение<br>для вашего ПК</h3>
            <p>Тихая работа и максимальная<br>производительность</p><span class="promo__link">Перейти <svg class="icon icon--small">
                  <use href="#icon-arrow"></use>
               </svg></span>
         </div>
         <div class="promo__fan"><i></i><i></i><i></i></div>
      </a>
      <a href="catalog.php" class="promo promo--memory">
         <div class="promo__content">
            <p class="eyebrow"><span></span> Быстрая доставка</p>
            <h3>Оперативная<br>память</h3>
            <p>Скорость, которая<br>впечатляет</p><span class="promo__link">Перейти <svg class="icon icon--small">
                  <use href="#icon-arrow"></use>
               </svg></span>
         </div>
         <div class="promo__ram promo__ram--one"></div>
         <div class="promo__ram promo__ram--two"></div>
      </a>
   </section>

   <section class="brands container">
      <div class="section-heading section-heading--compact">
         <h2>Бренды</h2><a href="catalog.php" class="text-link">Все бренды <svg class="icon icon--small">
               <use href="#icon-arrow"></use>
            </svg></a>
      </div>
      <div class="brands__list">
         <span>AMD</span><span>intel.</span><span>ASUS</span><span class="brand-msi">msi</span><span class="brand-gigabyte">GIGABYTE</span><span class="brand-kingston">KINGSTON</span><span class="brand-deepcool">DEEPCOOL</span>
      </div>
   </section>

   <section class="benefits container">
      <div class="benefit"><svg class="benefit__icon">
            <use href="#icon-truck"></use>
         </svg>
         <div><strong>Быстрая доставка</strong><span>по всей России</span></div>
      </div>
      <div class="benefit"><svg class="benefit__icon">
            <use href="#icon-shield"></use>
         </svg>
         <div><strong>Гарантия</strong><span>на все товары</span></div>
      </div>
      <div class="benefit"><svg class="benefit__icon">
            <use href="#icon-headset"></use>
         </svg>
         <div><strong>Профессиональная</strong><span>поддержка</span></div>
      </div>
      <div class="benefit"><svg class="benefit__icon">
            <use href="#icon-refresh"></use>
         </svg>
         <div><strong>Удобный возврат</strong><span>в течение 14 дней</span></div>
      </div>
   </section>
</main>

<footer class="site-footer">
   <div class="container site-footer__inner">
      <span>© <?= date('Y') ?> PC Builder</span>
      <span>Собери свой идеальный ПК</span>
   </div>
</footer>

</body>

</html>