<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/catalog.php';
require __DIR__ . '/app/orders.php';

if (!isAuthenticated()) {
    $_SESSION['auth_notice'] = 'Войдите или зарегистрируйтесь, чтобы оформить заказ.';
    header('Location: cart.php');
    exit;
}

$cart = [];
foreach (cartItems() as $id => $quantity) {
    $product = catalogProduct($pdo, (string) $id);
    if ($product !== null) {
        $product['quantity'] = $quantity;
        $product['line_total'] = (float) $product['price'] * $quantity;
        $cart[] = $product;
    }
}
if ($cart === []) {
    catalogFlash('Корзина пуста — сначала добавьте товары.', 'error');
    header('Location: cart.php');
    exit;
}

$total = array_sum(array_map(static fn (array $item): float => (float) $item['line_total'], $cart));

$errors = [];
$form = [
    'customer_name' => '',
    'phone' => '',
    'address' => '',
    'comment' => '',
    'payment_method' => 'card',
    'card_number' => '',
    'card_expiry' => '',
    'card_cvc' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $field => $_) {
        $form[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if (mb_strlen($form['customer_name'], 'UTF-8') < 2) {
        $errors['customer_name'] = 'Укажите имя получателя.';
    }
    if (!preg_match('/^[\d\s()+\-]{6,20}$/', $form['phone'])) {
        $errors['phone'] = 'Укажите корректный номер телефона.';
    }
    if (mb_strlen($form['address'], 'UTF-8') < 5) {
        $errors['address'] = 'Укажите адрес доставки.';
    }
    if (!in_array($form['payment_method'], ['card', 'cash'], true)) {
        $errors['payment_method'] = 'Выберите способ оплаты.';
    }

    
    
    if ($form['payment_method'] === 'card') {
        $cardDigits = preg_replace('/\D+/', '', $form['card_number']) ?? '';
        if (!orderLuhnValid($cardDigits)) {
            $errors['card_number'] = 'Проверьте номер карты: он должен содержать 13–19 цифр (алгоритм Луна).';
        }
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $form['card_expiry'])) {
            $errors['card_expiry'] = 'Срок действия указывается в формате ММ/ГГ.';
        }
        if (!preg_match('/^\d{3,4}$/', $form['card_cvc'])) {
            $errors['card_cvc'] = 'CVC — это 3 или 4 цифры на обороте карты.';
        }
    }

    if ($errors === []) {
        $status = $form['payment_method'] === 'card' ? ORDER_STATUS_PENDING_PAYMENT : ORDER_STATUS_NEW;
        $order = orderCreate($pdo, array_map(static fn (array $item): array => [
            'id' => (string) $item['id'],
            'name' => (string) $item['name'],
            'image_path' => (string) $item['image_path'],
            'price' => (float) $item['price'],
            'quantity' => (int) $item['quantity'],
        ], $cart), [
            'user_email' => (string) $_SESSION['user_email'],
            'customer_name' => $form['customer_name'],
            'phone' => $form['phone'],
            'address' => $form['address'],
            'comment' => $form['comment'],
            'payment_method' => $form['payment_method'],
            'status' => $status,
        ], $total);

        clearCart();
        catalogFlash(
            "Заказ {$order['number']} оформлен! Это учебный проект, поэтому приём реальных платежей недоступен: "
            . 'оплата не проводится, заказ сохранён со статусом «' . orderStatusLabel($status) . '». '
            . 'Посмотреть заказ можно в личном кабинете.'
        );
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Оформление заказа — PC Builder';
$pageDescription = 'Оформление заказа в магазине PC Builder: укажите данные получателя, способ доставки и подтвердите покупку комплектующих.';
require __DIR__ . '/app/views/header.php';
?>
<main class="simple-page container checkout-page">
    <svg class="svg-sprite" aria-hidden="true"><symbol id="icon-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></symbol><symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.8 8.9c0 5.1-8.8 10-8.8 10s-8.8-4.9-8.8-10A4.6 4.6 0 0 1 12 6.1a4.6 4.6 0 0 1 8.8 2.8Z"></path></symbol><symbol id="icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 21c.5-3.7 2.8-5.6 7-5.6s6.5 1.9 7 5.6"></path></symbol><symbol id="icon-cart" viewBox="0 0 24 24"><path d="M3.5 5h2l1.6 10.1h10.8l2-7.2H7"></path><circle cx="9" cy="19.5" r="1.2"></circle><circle cx="17" cy="19.5" r="1.2"></circle></symbol></svg>
    <div class="simple-page__heading">
        <div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><a href="cart.php">Корзина</a><span>›</span><span>Оформление заказа</span></div>
        <h1>Оформление заказа</h1>
        <p>Заполните контактные данные и выберите способ оплаты.</p>
    </div>

    <form method="post" class="cart-layout checkout-layout" id="checkout-form" novalidate>
        <section class="checkout-fields">
            <h2 class="checkout-section__title">1. Получатель</h2>
            <div class="checkout-grid">
                <label class="checkout-field<?= isset($errors['customer_name']) ? ' checkout-field--error' : '' ?>">
                    <span>Имя и фамилия</span>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($form['customer_name'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Иван Иванов" maxlength="120" required>
                    <?php if (isset($errors['customer_name'])): ?><em><?= $errors['customer_name'] ?></em><?php endif; ?>
                </label>
                <label class="checkout-field<?= isset($errors['phone']) ? ' checkout-field--error' : '' ?>">
                    <span>Телефон</span>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($form['phone'], ENT_QUOTES, 'UTF-8') ?>" placeholder="+7 (900) 000-00-00" maxlength="20" required>
                    <?php if (isset($errors['phone'])): ?><em><?= $errors['phone'] ?></em><?php endif; ?>
                </label>
            </div>
            <label class="checkout-field<?= isset($errors['address']) ? ' checkout-field--error' : '' ?>">
                <span>Адрес доставки</span>
                <input type="text" name="address" value="<?= htmlspecialchars($form['address'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Город, улица, дом, квартира" maxlength="255" required>
                <?php if (isset($errors['address'])): ?><em><?= $errors['address'] ?></em><?php endif; ?>
            </label>
            <label class="checkout-field">
                <span>Комментарий к заказу (необязательно)</span>
                <textarea name="comment" rows="3" maxlength="500" placeholder="Например: позвоните за час до доставки"><?= htmlspecialchars($form['comment'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>

            <h2 class="checkout-section__title">2. Оплата</h2>
            <?php if (isset($errors['payment_method'])): ?><div class="flash flash--error"><?= $errors['payment_method'] ?></div><?php endif; ?>
            <div class="checkout-payments">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="card" <?= $form['payment_method'] !== 'cash' ? 'checked' : '' ?> data-payment-toggle>
                    <span class="payment-option__body"><strong>Банковская карта</strong><small>Visa, MasterCard, МИР — онлайн</small></span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cash" <?= $form['payment_method'] === 'cash' ? 'checked' : '' ?> data-payment-toggle>
                    <span class="payment-option__body"><strong>Оплата при получении</strong><small>Наличными или картой у курьера</small></span>
                </label>
            </div>

            <div class="checkout-card-fields<?= $form['payment_method'] === 'cash' ? ' is-hidden' : '' ?>" id="card-fields">
                <label class="checkout-field<?= isset($errors['card_number']) ? ' checkout-field--error' : '' ?>">
                    <span>Номер карты</span>
                    <input type="text" name="card_number" id="card-number" inputmode="numeric" autocomplete="cc-number" value="<?= htmlspecialchars($form['card_number'], ENT_QUOTES, 'UTF-8') ?>" placeholder="4111 1111 1111 1111" maxlength="23">
                    <?php if (isset($errors['card_number'])): ?><em><?= $errors['card_number'] ?></em><?php endif; ?>
                </label>
                <div class="checkout-grid">
                    <label class="checkout-field<?= isset($errors['card_expiry']) ? ' checkout-field--error' : '' ?>">
                        <span>Срок действия</span>
                        <input type="text" name="card_expiry" id="card-expiry" inputmode="numeric" autocomplete="cc-exp" value="<?= htmlspecialchars($form['card_expiry'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ММ/ГГ" maxlength="5">
                        <?php if (isset($errors['card_expiry'])): ?><em><?= $errors['card_expiry'] ?></em><?php endif; ?>
                    </label>
                    <label class="checkout-field<?= isset($errors['card_cvc']) ? ' checkout-field--error' : '' ?>">
                        <span>CVC / CVV</span>
                        <input type="password" name="card_cvc" inputmode="numeric" autocomplete="cc-csc" value="<?= htmlspecialchars($form['card_cvc'], ENT_QUOTES, 'UTF-8') ?>" placeholder="•••" maxlength="4">
                        <?php if (isset($errors['card_cvc'])): ?><em><?= $errors['card_cvc'] ?></em><?php endif; ?>
                    </label>
                </div>
                <p class="checkout-card-note">Реальное списание средств не производится. Данные карты используются только для демонстрации и не сохраняются в базе. Для демонстрации подойдут любые данные, карта - 4111-1111-1111-1111</p>
            </div>

            <button type="submit" class="button button--primary cart-checkout" id="checkout-submit"><?= $total > 0 ? 'Оплатить ' . catalogPrice($total) : 'Оформить заказ' ?></button>
        </section>

        <aside class="cart-summary">
            <h2>Ваш заказ</h2>
            <?php foreach ($cart as $item): ?>
                <div class="cart-summary__item">
                    <span><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?> × <?= (int) $item['quantity'] ?></span>
                    <strong><?= catalogPrice((float) $item['line_total']) ?></strong>
                </div>
            <?php endforeach; ?>
            <hr>
            <div><span>Доставка</span><strong class="summary-free">Бесплатно</strong></div>
            <div class="cart-summary__total"><span>Итого</span><strong><?= catalogPrice($total) ?></strong></div>
            <p class="cart-summary__note">Нажимая «Оплатить», вы соглашаетесь с условиями учебного магазина.</p>
        </aside>
    </form>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
<script>
(function () {
    var form = document.getElementById('checkout-form');
    if (!form) return;
    var cardFields = document.getElementById('card-fields');
    form.querySelectorAll('[data-payment-toggle]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            cardFields.classList.toggle('is-hidden', radio.value !== 'card' && radio.checked);
            if (radio.value === 'card' && radio.checked) cardFields.classList.remove('is-hidden');
            if (radio.value === 'cash' && radio.checked) cardFields.classList.add('is-hidden');
        });
    });
    var number = document.getElementById('card-number');
    number.addEventListener('input', function () {
        var digits = number.value.replace(/\D/g, '').slice(0, 19);
        number.value = digits.replace(/(.{4})/g, '$1 ').trim();
    });
    var expiry = document.getElementById('card-expiry');
    expiry.addEventListener('input', function () {
        var digits = expiry.value.replace(/\D/g, '').slice(0, 4);
        expiry.value = digits.length > 2 ? digits.slice(0, 2) + '/' + digits.slice(2) : digits;
    });
    var submit = document.getElementById('checkout-submit');
    form.addEventListener('submit', function (event) {
        if (submit.disabled) {
            event.preventDefault();
            return;
        }
        event.preventDefault();
        submit.disabled = true;
        submit.textContent = 'Обработка платежа…';
        setTimeout(function () {
            form.submit();
        }, 1200);
    });
})();
</script>
</body>
</html>
