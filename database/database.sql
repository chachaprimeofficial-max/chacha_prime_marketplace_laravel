-- Chacha Prime Marketplace single manual MySQL schema source.
-- No Laravel migration files are used.
CREATE DATABASE IF NOT EXISTS chacha_prime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chacha_prime;

CREATE TABLE IF NOT EXISTS users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role VARCHAR(40) NOT NULL DEFAULT 'customer',
 status VARCHAR(30) NOT NULL DEFAULT 'active',
 email_verified_at TIMESTAMP NULL,
 two_factor_enabled TINYINT(1) NOT NULL DEFAULT 1,
 totp_secret TEXT NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 INDEX idx_users_role_status (role,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Additional marketplace tables will be added in the database build phase:
-- vendors, products, categories, pricing tiers, carts, orders, payments,
-- wallets, virtual marketplace cards, group buying, live commerce, reviews,
-- coupons, shipping, AI logs, notifications, CMS, settings, audit logs,
-- localization and permissions.
