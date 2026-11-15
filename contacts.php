<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/app/config/database.php';
require __DIR__ . '/app/auth.php';

$authError = handleAuthRequest($pdo);
$pageTitle = 'Контакты — PC Builder';
require __DIR__ . '/app/views/header.php';
?>
<main class="contacts-page">
    <section class="contacts-hero container">
        <div class="breadcrumbs"><a href="index.php">Главная</a><span>›</span><span>Контакты</span></div>
        <div class="contacts-hero__content">
            <div>
                <p class="eyebrow"><span></span> Мы рядом</p>
                <h1>Поможем собрать<br><em>идеальный ПК</em></h1>
                <p>Подскажем совместимые комплектующие, рассчитаем стоимость сборки и ответим на вопросы по доставке.</p>
            </div>
            <div class="contacts-hero__orb" aria-hidden="true"><span>PC<br>BUILDER</span></div>
        </div>
    </section>
    <section class="contacts-content container">
        <div class="contact-cards">
            <a class="contact-card contact-card--accent" href="tel:+78005553535"><span class="contact-card__icon">☎</span><span><small>Позвоните нам</small><strong>8 800 555-35-35</strong><em>Ежедневно с 9:00 до 21:00</em></span></a>
            <a class="contact-card" href="mailto:hello@pc-builder.local"><span class="contact-card__icon">@</span><span><small>Напишите нам</small><strong>hello@pc-builder.local</strong><em>Ответим в течение рабочего дня</em></span></a>
            <div class="contact-card"><span class="contact-card__icon">⌖</span><span><small>Приезжайте в шоурум</small><strong>Москва, ул. Техническая, 12</strong><em>Самовывоз и консультация по сборке</em></span></div>
        </div>
        <div class="contacts-columns">
            <section class="contact-form-card"><p class="eyebrow"><span></span> Обратная связь</p><h2>Есть вопрос?</h2><p class="contact-form-card__lead">Оставьте заявку, и специалист свяжется с вами в удобное время.</p><form class="contact-form" action="mailto:hello@pc-builder.local" method="post" enctype="text/plain"><label>Ваше имя<input type="text" name="name" placeholder="Как к вам обращаться?" required></label><label>Телефон или email<input type="text" name="contact" placeholder="Для ответа" required></label><label>Сообщение<textarea name="message" rows="4" placeholder="Расскажите, чем помочь" required></textarea></label><button class="button button--primary" type="submit">Отправить сообщение <span>→</span></button></form></section>
            <section class="faq-card"><p class="eyebrow"><span></span> Частые вопросы</p><h2>Перед обращением</h2><details open><summary>Поможете подобрать комплектующие?</summary><p>Да. Напишите задачу и бюджет — мы предложим совместимую конфигурацию.</p></details><details><summary>Можно ли забрать заказ самостоятельно?</summary><p>Да, доступен самовывоз из шоурума в Москве после подтверждения заказа.</p></details><details><summary>Как быстро доставите заказ?</summary><p>По Москве — от 1 дня, в другие города отправляем транспортной компанией.</p></details></section>
        </div>
    </section>
</main>
<footer class="site-footer"><div class="container site-footer__inner"><span>© <?= date('Y') ?> PC Builder</span><span>Собери свой идеальный ПК</span></div></footer>
</body>
</html>
