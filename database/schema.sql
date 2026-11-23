DROP DATABASE IF EXISTS pc_builder;
CREATE DATABASE pc_builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pc_builder;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE homepage_media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_key VARCHAR(50) NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(180) NOT NULL DEFAULT '',
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
    reviews_count INT UNSIGNED NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    badge VARCHAR(40) NOT NULL,
    badge_class VARCHAR(30) NOT NULL DEFAULT 'badge--orange',
    art VARCHAR(30) NOT NULL DEFAULT 'product',
    image_path VARCHAR(255) NOT NULL DEFAULT 'assets/images/product-placeholder.svg',
    category_slug VARCHAR(40) NOT NULL DEFAULT 'peripherals',
    brand VARCHAR(80) NOT NULL DEFAULT 'Другой бренд',
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    description TEXT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    user_email VARCHAR(120) NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    address VARCHAR(255) NOT NULL,
    comment TEXT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'card',
    status VARCHAR(30) NOT NULL DEFAULT 'new',
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_orders_user_email (user_email)
);

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id VARCHAR(40) NULL,
    product_name VARCHAR(180) NOT NULL,
    product_image VARCHAR(255) NOT NULL DEFAULT '',
    price DECIMAL(10,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO homepage_media (media_key, image_path, alt_text, is_active) VALUES
    ('logo', 'assets/images/logo.png', 'PC Builder', 1),
    ('hero', 'assets/images/hero-img.png', 'Комплектующие для игрового компьютера', 1),
    ('promo_cooling', 'assets/images/cpu-fan_homePage.png', 'Система охлаждения для компьютера', 1),
    ('promo_memory', 'assets/images/ram_homePage.png', 'Оперативная память', 1);

INSERT INTO products (name, rating, reviews_count, price, old_price, badge, badge_class, art, image_path, category_slug, brand, in_stock, description, is_featured) VALUES
    ('AMD Ryzen 7 7800X3D', 4.9, 1234, 37990, NULL, 'Хит продаж', 'badge--orange', 'cpu', 'assets/images/7800x3d.png', 'processors', 'AMD', 1, 'Игровой процессор с большим 3D-кэшем для высокой частоты кадров.', 1),
    ('GeForce RTX 4080 SUPER', 4.9, 856, 129990, NULL, 'Новинка', 'badge--green', 'gpu', 'assets/images/5080.png', 'gpu', 'NVIDIA', 1, 'Флагманская видеокарта для современных игр и профессиональной графики.', 1),
    ('MSI MAG B650 TOMAHAWK WIFI', 4.8, 743, 19990, 22990, '-12%', 'badge--red', 'motherboard', 'assets/images/b650 msi.png', 'motherboards', 'MSI', 1, 'Материнская плата на чипсете B650 для сбалансированной сборки AMD.', 1),
    ('DeepCool PX1000G', 4.9, 420, 14990, NULL, 'Хит продаж', 'badge--orange', 'power', 'assets/images/deepcool-1000.png', 'power', 'DeepCool', 1, 'Мощный модульный блок питания с тихим охлаждением.', 1),
    ('Процессор Intel Core i7-14700K', 4.9, 842, 49990, NULL, 'Хит', 'badge--orange', 'cpu', 'assets/images/i7-14700K.jpg', 'processors', 'Intel', 1, 'Производительный процессор для игр, стриминга и рабочих задач.', 0),
    ('Процессор AMD Ryzen 7 9700X', 4.9, 521, 44990, NULL, 'Новинка', 'badge--green', 'cpu', 'assets/images/ryzen-9700x.jpg', 'processors', 'AMD', 1, 'Современная восьмиядерная платформа для быстрой сборки нового поколения.', 0),
    ('Материнская плата MSI B760 GAMING PLUS WIFI', 4.7, 418, 22990, 25990, 'Акция', 'badge--red', 'motherboard', 'assets/images/MSI B760.jpg', 'motherboards', 'MSI', 1, 'Надёжная плата с Wi-Fi для игровых систем на Intel.', 0),
    ('Видеокарта ASUS GeForce RTX 4070 SUPER', 4.8, 621, 79990, NULL, 'Хит', 'badge--orange', 'gpu', 'assets/images/rtx-4070.jpg', 'gpu', 'ASUS', 1, 'Тихая видеокарта для игр в высоком разрешении и создания контента.', 0),
    ('Оперативная память Kingston FURY Beast 32GB', 4.8, 379, 12990, NULL, 'Популярное', 'badge--blue', 'ram', 'assets/images/kingston-32gb.jpg', 'ram', 'Kingston', 1, 'Комплект быстрой памяти DDR5 с запасом для игр и многозадачности.', 0),
    ('SSD накопитель Samsung 990 PRO 1TB', 4.9, 770, 16990, NULL, 'Хит', 'badge--orange', 'ssd', 'assets/images/samsung-990.jpg', 'storage', 'Samsung', 1, 'Быстрый NVMe-накопитель для системы, игр и больших проектов.', 0),
    ('Система охлаждения DeepCool AK620', 4.7, 352, 8990, NULL, 'Популярное', 'badge--blue', 'cooling', 'assets/images/deepcool-ak620.jpg', 'cooling', 'DeepCool', 1, 'Башенный кулер для стабильной и тихой работы производительного ПК.', 0),
    ('Корпус Cougar MX600 RGB', 4.8, 247, 11990, NULL, 'Новинка', 'badge--green', 'case', 'assets/images/cougar-mx600.jpg', 'cases', 'Cougar', 1, 'Просторный корпус с хорошей продуваемостью и подсветкой.', 0);
