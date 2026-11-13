<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';

$authError = handleAuthRequest($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['store_action'] ?? '');
    $productId = trim((string) ($_POST['product_id'] ?? ''));
    if ($action === 'update_cart' && $productId !== '') {
        setCartItemQuantity($productId, (int) ($_POST['quantity'] ?? 0));
        catalogFlash('Количество товара обновлено.');
    } elseif ($action === 'remove_cart' && $productId !== '') {
        removeCartItem($productId);
        catalogFlash('Товар удалён из корзины.');
    } elseif ($action === 'clear_cart') {
        clearCart();
        catalogFlash('Корзина очищена.');
    } elseif ($action === 'add_cart' && $productId !== '' && catalogProduct($pdo, $productId) !== null) {
        addCartItem($productId);
        catalogFlash('Товар добавлен в корзину.');
    }
    catalogRedirectBack('cart.php');
}

$cart = [];
foreach (cartItems() as $id => $quantity) {
    $product = catalogProduct($pdo, $id);
    if ($product !== null) {
        $product['quantity'] = $quantity;
        $product['line_total'] = (float) $product['price'] * $quantity;
        $cart[] = $product;
    }
}
$total = array_sum(array_map(static fn (array $item): float => (float) $item['line_total'], $cart));
$delivery = $total > 0 ? 0 : 0;
$pageTitle = 'Корзина — PC Builder';
$flash = consumeCatalogFlash();
require __DIR__ . '/app/views/header.php';
?>
<main class="simple-page container cart-page">
    <svg class="svg-sprite" aria-hidden="true"><symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol><symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol><symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol><symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol></svg>
    <div class="simple-page__heading"><div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><span>Корзина</span></div><h1>Корзина</h1><p><?= $cart ? 'Проверьте товары перед оформлением заказа.' : 'Добавьте товары из каталога, чтобы оформить заказ.' ?></p></div>
    <?php if ($flash): ?><div class="flash flash--<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($cart): ?>
        <div class="cart-layout"><section class="cart-items"><div class="cart-items__head"><strong><?= cartItemCount() ?> товар<?= cartItemCount() === 1 ? '' : 'ов' ?></strong><form method="post"><input type="hidden" name="store_action" value="clear_cart"><button type="submit" class="text-button">Очистить корзину</button></form></div><?php foreach ($cart as $item): ?><article class="cart-item"><a class="cart-item__image" href="product.php?id=<?= urlencode((string) $item['id']) ?>"><img src="<?= htmlspecialchars($item['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"></a><div class="cart-item__info"><h2><a href="product.php?id=<?= urlencode((string) $item['id']) ?>"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></a></h2><p><?= htmlspecialchars((string) $item['brand'], ENT_QUOTES, 'UTF-8') ?> · <?= (int) $item['in_stock'] === 1 ? 'В наличии' : 'Под заказ' ?></p><strong><?= catalogPrice((float) $item['price']) ?></strong></div><form method="post" class="quantity-control"><input type="hidden" name="store_action" value="update_cart"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"><label>Количество<input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" max="99" onchange="this.form.submit()"></label></form><div class="cart-item__total"><?= catalogPrice((float) $item['line_total']) ?><form method="post"><input type="hidden" name="store_action" value="remove_cart"><input type="hidden" name="product_id" value="<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>"><button type="submit" class="remove-button" aria-label="Удалить товар">×</button></form></div></article><?php endforeach; ?></section><aside class="cart-summary"><h2>Ваш заказ</h2><div><span>Товары</span><strong><?= catalogPrice($total) ?></strong></div><div><span>Доставка</span><strong class="summary-free">Бесплатно</strong></div><hr><div class="cart-summary__total"><span>Итого</span><strong><?= catalogPrice($total + $delivery) ?></strong></div><button type="button" class="button button--primary cart-checkout" data-checkout>Перейти к оформлению</button><p class="cart-summary__note">Доставка по России от 1 дня. Точную дату сообщим при оформлении.</p></aside></div>
    <?php else: ?><div class="catalog-empty"><svg class="empty-cart-icon" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></svg><h2>Корзина пуста</h2><p>Выберите комплектующие в каталоге, и они появятся здесь.</p><a href="catalog.php" class="button button--primary">Перейти в каталог</a></div><?php endif; ?>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
<script>document.querySelector('[data-checkout]')?.addEventListener('click',function(){alert('Оформление заказа будет доступно после заполнения контактных данных.');});</script>
</body>
</html>
