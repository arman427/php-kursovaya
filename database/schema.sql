CREATE DATABASE IF NOT EXISTS pc_builder CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pc_builder;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS homepage_media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_key VARCHAR(50) NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(180) NOT NULL DEFAULT '',
    is_active TINYINT(1) NOT NULL DEFAULT 1
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
    image_path VARCHAR(255) NOT NULL DEFAULT 'assets/images/product.png',
    category_slug VARCHAR(40) NOT NULL DEFAULT 'peripherals',
    brand VARCHAR(80) NOT NULL DEFAULT 'Другой бренд',
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    description TEXT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1
);

-- Миграция для базы, созданной до появления image_path в products.
SET @image_path_column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'image_path'
);
SET @add_image_path_sql = IF(
    @image_path_column_exists = 0,
    'ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NOT NULL DEFAULT ''assets/images/product.png'' AFTER art',
    'SELECT 1'
);
PREPARE add_image_path_statement FROM @add_image_path_sql;
EXECUTE add_image_path_statement;
DEALLOCATE PREPARE add_image_path_statement;

-- Миграции для дополнительных полей каталога в уже существующей базе.
SET @category_slug_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'category_slug'
);
SET @add_category_slug_sql = IF(
    @category_slug_column_exists = 0,
    'ALTER TABLE products ADD COLUMN category_slug VARCHAR(40) NOT NULL DEFAULT ''peripherals'' AFTER image_path',
    'SELECT 1'
);
PREPARE add_category_slug_statement FROM @add_category_slug_sql;
EXECUTE add_category_slug_statement;
DEALLOCATE PREPARE add_category_slug_statement;

SET @brand_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'brand'
);
SET @add_brand_sql = IF(
    @brand_column_exists = 0,
    'ALTER TABLE products ADD COLUMN brand VARCHAR(80) NOT NULL DEFAULT ''Другой бренд'' AFTER category_slug',
    'SELECT 1'
);
PREPARE add_brand_statement FROM @add_brand_sql;
EXECUTE add_brand_statement;
DEALLOCATE PREPARE add_brand_statement;

SET @in_stock_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'in_stock'
);
SET @add_in_stock_sql = IF(
    @in_stock_column_exists = 0,
    'ALTER TABLE products ADD COLUMN in_stock TINYINT(1) NOT NULL DEFAULT 1 AFTER brand',
    'SELECT 1'
);
PREPARE add_in_stock_statement FROM @add_in_stock_sql;
EXECUTE add_in_stock_statement;
DEALLOCATE PREPARE add_in_stock_statement;

SET @description_column_exists = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'description'
);
SET @add_description_sql = IF(
    @description_column_exists = 0,
    'ALTER TABLE products ADD COLUMN description TEXT NULL AFTER in_stock',
    'SELECT 1'
);
PREPARE add_description_statement FROM @add_description_sql;
EXECUTE add_description_statement;
DEALLOCATE PREPARE add_description_statement;

UPDATE products
SET
    category_slug = CASE
        WHEN name REGEXP 'Ryzen|Core i|Xeon|процессор' THEN 'processors'
        WHEN name REGEXP 'RTX|GTX|Radeon|GeForce|видеокарт' THEN 'gpu'
        WHEN name REGEXP 'B650|B760|Z790|Tomahawk|материнск' THEN 'motherboards'
        WHEN name REGEXP 'RAM|FURY|DDR|оперативн|памят' THEN 'ram'
        WHEN name REGEXP 'SSD|990 PRO|NVMe|накопител|HDD' THEN 'storage'
        WHEN name REGEXP 'PX1000|блок питан|Power Supply' THEN 'power'
        WHEN name REGEXP 'AK620|Cool|охлажд' THEN 'cooling'
        WHEN name REGEXP 'Cougar|корпус|Case' THEN 'cases'
        ELSE category_slug
    END,
    brand = CASE
        WHEN name LIKE '%AMD%' OR name LIKE '%Ryzen%' THEN 'AMD'
        WHEN name LIKE '%Intel%' OR name LIKE '%Core i%' THEN 'Intel'
        WHEN name LIKE '%NVIDIA%' OR name LIKE '%GeForce%' THEN 'NVIDIA'
        WHEN name LIKE '%ASUS%' THEN 'ASUS'
        WHEN name LIKE '%MSI%' THEN 'MSI'
        WHEN name LIKE '%Kingston%' THEN 'Kingston'
        WHEN name LIKE '%Samsung%' THEN 'Samsung'
        WHEN name LIKE '%DeepCool%' THEN 'DeepCool'
        WHEN name LIKE '%Cougar%' THEN 'Cougar'
        ELSE brand
    END;


INSERT INTO homepage_media (media_key, image_path, alt_text, is_active)
VALUES
    ('logo', 'assets/images/logo.png', 'PC Builder', 1),
    ('hero', 'assets/images/hero-img.png', 'Комплектующие для игрового компьютера', 1),
    ('promo_cooling', 'assets/images/cpu-fan_homePage.png', 'Система охлаждения для компьютера', 1),
    ('promo_memory', 'assets/images/ram_homePage.png', 'Оперативная память', 1)
ON DUPLICATE KEY UPDATE
    image_path = VALUES(image_path),
    alt_text = VALUES(alt_text),
    is_active = VALUES(is_active);

INSERT INTO products (name, rating, reviews_count, price, old_price, badge, badge_class, art, image_path, category_slug, brand, in_stock, description, is_featured)
SELECT * FROM (
    SELECT 'AMD Ryzen 7 7800X3D' AS name, 4.9 AS rating, 1234 AS reviews_count, 37990 AS price, NULL AS old_price, 'Хит продаж' AS badge, 'badge--orange' AS badge_class, 'cpu' AS art, 'assets/images/7800x3d.png' AS image_path, 'processors' AS category_slug, 'AMD' AS brand, 1 AS in_stock, 'Игровой процессор с большим 3D-кэшем для высокой частоты кадров.' AS description, 1 AS is_featured
    UNION ALL SELECT 'GeForce RTX 4080 SUPER', 4.9, 856, 129990, NULL, 'Новинка', 'badge--green', 'gpu', 'assets/images/5080.png', 'gpu', 'NVIDIA', 1, 'Флагманская видеокарта для современных игр и профессиональной графики.', 1
    UNION ALL SELECT 'MSI MAG B650 TOMAHAWK WIFI', 4.8, 743, 19990, 22990, '-12%', 'badge--red', 'motherboard', 'assets/images/b650 msi.png', 'motherboards', 'MSI', 1, 'Материнская плата на чипсете B650 для сбалансированной сборки AMD.', 1
    UNION ALL SELECT 'DeepCool PX1000G', 4.9, 420, 14990, NULL, 'Хит продаж', 'badge--orange', 'power', 'assets/images/deepcool-1000.png', 'power', 'DeepCool', 1, 'Мощный модульный блок питания с тихим охлаждением.', 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM products LIMIT 1);

-- Привязываем изображения к уже существующим товарам после обновления старой схемы.
UPDATE products
SET image_path = CASE name
    WHEN 'AMD Ryzen 7 7800X3D' THEN 'assets/images/7800x3d.png'
    WHEN 'GeForce RTX 4080 SUPER' THEN 'assets/images/5080.png'
    WHEN 'MSI MAG B650 TOMAHAWK WIFI' THEN 'assets/images/b650 msi.png'
    WHEN 'DeepCool PX1000G' THEN 'assets/images/deepcool-1000.png'
    ELSE image_path
END
WHERE name IN (
    'AMD Ryzen 7 7800X3D',
    'GeForce RTX 4080 SUPER',
    'MSI MAG B650 TOMAHAWK WIFI',
    'DeepCool PX1000G'
);

-- Нормализуем подписи карточек, если предыдущая версия seed-файла была импортирована с неверной кодировкой.
UPDATE products
SET badge = CASE name
    WHEN 'AMD Ryzen 7 7800X3D' THEN 'Хит продаж'
    WHEN 'GeForce RTX 4080 SUPER' THEN 'Новинка'
    WHEN 'MSI MAG B650 TOMAHAWK WIFI' THEN '-12%'
    WHEN 'DeepCool PX1000G' THEN 'Хит продаж'
    ELSE badge
END
WHERE name IN (
    'AMD Ryzen 7 7800X3D',
    'GeForce RTX 4080 SUPER',
    'MSI MAG B650 TOMAHAWK WIFI',
    'DeepCool PX1000G'
);
