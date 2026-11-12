CREATE DATABASE IF NOT EXISTS pc_builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pc_builder;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
    reviews_count INT UNSIGNED NOT NULL DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) NULL,
    badge VARCHAR(40) NOT NULL,
    badge_class VARCHAR(30) NOT NULL DEFAULT 'badge--orange',
    art VARCHAR(30) NOT NULL DEFAULT 'product',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO products (name, rating, reviews_count, price, old_price, badge, badge_class, art, is_featured)
SELECT * FROM (
    SELECT 'AMD Ryzen 7 7800X3D', 4.9, 1234, 37990, NULL, 'Хит продаж', 'badge--orange', 'cpu', 1
    UNION ALL SELECT 'GeForce RTX 4080 SUPER', 4.9, 856, 129990, NULL, 'Новинка', 'badge--green', 'gpu', 1
    UNION ALL SELECT 'MSI MAG B650 TOMAHAWK WIFI', 4.8, 743, 19990, 22990, '-12%', 'badge--red', 'motherboard', 1
    UNION ALL SELECT 'DeepCool PX1000G', 4.9, 420, 14990, NULL, 'Хит продаж', 'badge--orange', 'power', 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM products LIMIT 1);
