-- ========================================================
-- TRIO 3D Printing E-Commerce & Workshop Studio
-- Complete SQL Dump ready for InfinityFree & phpMyAdmin
-- Admin users should be created by the application seeder with TRIO_ADMIN_PASSWORD configured.
-- ========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'super_admin',
  `permissions` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `filaments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `filaments`;
CREATE TABLE `filaments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spool_price` decimal(10,2) NOT NULL,
  `spool_weight_grams` decimal(10,2) NOT NULL DEFAULT 1000.00,
  `cost_per_gram` decimal(10,4) NOT NULL,
  `color_hint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#c8f228',
  `available_colors` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `filaments` (`id`, `name`, `spool_price`, `spool_weight_grams`, `cost_per_gram`, `color_hint`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PLA+ (عالي التحمل والتشطيب)', 750.00, 1000.00, 0.7500, '#c8f228', 1, NOW(), NOW()),
(2, 'PLA قياسي (Standard)', 675.00, 1000.00, 0.6750, '#edf2f1', 1, NOW(), NOW()),
(3, 'PETG (مقاوم للحرارة والرطوبة)', 850.00, 1000.00, 0.8500, '#ef8950', 1, NOW(), NOW()),
(4, 'TPU مرن (Flexible Rubber)', 1100.00, 1000.00, 1.1000, '#6ab8db', 1, NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  `color_options` json DEFAULT NULL,
  `color_label` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filament_id` bigint(20) UNSIGNED DEFAULT 1,
  `weight_grams` decimal(10,2) NOT NULL,
  `dimensions` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `multiplier` decimal(5,2) NOT NULL DEFAULT 4.00,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_filament_id_foreign` (`filament_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `description`, `details`, `image_path`, `gallery_images`, `color_options`, `color_label`, `filament_id`, `weight_grams`, `dimensions`, `base_price`, `multiplier`, `price`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Orbit Stand', 'حامل موبايل هندسي قابل للتعديل مكتبي', 'تصميم متوازن ومجرب مع كافة الهواتف الذكية.', NULL, NULL, '["برتقالي", "أسود", "أخضر ليموني"]', 'لون الحامل', 1, 90.00, '120 × 85 × 110 mm', 67.50, 4.00, 270.00, 1, 0, NOW(), NOW()),
(2, 'Loop Dock', 'منظّم كابلات ومكتب ثلاثي الأبعاد متين', 'منظم أنيق ومدمج للمكتب وأسلاك الشحن.', NULL, NULL, '["أسود غير لامع", "أبيض عاجي", "رمادي معدني"]', 'لون المنظم', 1, 115.00, '140 × 90 × 45 mm', 86.25, 4.00, 345.00, 1, 1, NOW(), NOW()),
(3, 'Stack Planter', 'أصيص نباتات مكتبي مفرغ ثلاثي الطبقات', 'أصيص مفرغ يسمح بتصريف وتهوية التربة.', NULL, NULL, '["قاعدة سوداء / جسم ليموني", "رمادي رخامي", "أخضر زيتوني"]', 'لون الأصيص', 1, 104.00, '100 × 100 × 95 mm', 78.00, 4.00, 312.00, 1, 2, NOW(), NOW());

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `weight_grams` decimal(10,2) NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `custom_quotes`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `custom_quotes`;
CREATE TABLE `custom_quotes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filament_id` bigint(20) UNSIGNED DEFAULT NULL,
  `filament_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quality` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Standard 0.20mm',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lime',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estimated_weight_grams` decimal(10,2) NOT NULL,
  `multiplier` decimal(5,2) NOT NULL DEFAULT 4.00,
  `estimated_price` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_quotes_quote_number_unique` (`quote_number`),
  KEY `custom_quotes_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Standard Laravel helper tables: cache, jobs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
