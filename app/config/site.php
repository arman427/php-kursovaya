<?php

declare(strict_types=1);

// Единая точка настроек для продвижения сайта.
return [
    'site_name' => 'PC Builder',
    'default_description' => 'PC Builder — интернет-магазин комплектующих для ПК: процессоры, видеокарты, материнские платы, память и накопители. Гарантия 12 месяцев и доставка от 1 дня.',
    'default_image' => 'assets/images/logo.png',

    // Номер счётчика Яндекс.Метрики. Пока значение 0, счётчик на страницы не выводится.
    'metrika_id' => 0,

    // Необязательно: имя аккаунта в Twitter для карточек, например '@pcbuilder'.
    'twitter_site' => '',

    // Официальные страницы в социальных сетях. Замените адреса на реальные.
    'social' => [
        ['short' => 'VK', 'title' => 'ВКонтакте', 'url' => 'https://vk.com/pcbuilder', 'note' => 'Новости, акции и обзоры комплектующих'],
        ['short' => 'TG', 'title' => 'Telegram', 'url' => 'https://t.me/pcbuilder', 'note' => 'Быстрые анонсы и подборки сборок'],
        ['short' => 'YT', 'title' => 'YouTube', 'url' => 'https://www.youtube.com/@pcbuilder', 'note' => 'Видеообзоры и сборка ПК шаг за шагом'],
    ],
];
