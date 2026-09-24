-- CHACHA PRIME country marketplace extension
-- Run this once if your production database is managed from database/database.sql
CREATE TABLE IF NOT EXISTS countries (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 code CHAR(2) NOT NULL UNIQUE,
 name VARCHAR(100) NOT NULL,
 flag VARCHAR(10) NULL,
 currency_code CHAR(3) NOT NULL DEFAULT 'USD',
 active TINYINT(1) NOT NULL DEFAULT 1,
 sort_order INT UNSIGNED NOT NULL DEFAULT 0,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 INDEX idx_countries_active_sort (active,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users ADD COLUMN IF NOT EXISTS country_id BIGINT UNSIGNED NULL;
ALTER TABLE products ADD COLUMN IF NOT EXISTS country_id BIGINT UNSIGNED NULL;
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_country (country_id);
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_products_country (country_id);

CREATE TABLE IF NOT EXISTS product_marketplaces (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id BIGINT UNSIGNED NOT NULL,
 country_id BIGINT UNSIGNED NOT NULL,
 currency CHAR(3) NULL,
 retail_price DECIMAL(18,2) NULL,
 wholesale_price DECIMAL(18,2) NULL,
 shipping_price DECIMAL(18,2) NOT NULL DEFAULT 0,
 tax_rate DECIMAL(8,4) NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 stock DECIMAL(18,3) NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 UNIQUE KEY uq_product_marketplace_country(product_id,country_id),
 INDEX idx_marketplace_country_active(country_id,active),
 CONSTRAINT fk_pm_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
 CONSTRAINT fk_pm_country FOREIGN KEY(country_id) REFERENCES countries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO countries(code,name,flag,currency_code,active,sort_order,created_at,updated_at) VALUES
('PK','Pakistan','🇵🇰','PKR',1,1,NOW(),NOW()),('CN','China','🇨🇳','CNY',1,2,NOW(),NOW()),
('AE','United Arab Emirates','🇦🇪','AED',1,3,NOW(),NOW()),('SA','Saudi Arabia','🇸🇦','SAR',1,4,NOW(),NOW()),
('US','United States','🇺🇸','USD',1,5,NOW(),NOW()),('GB','United Kingdom','🇬🇧','GBP',1,6,NOW(),NOW()),
('CA','Canada','🇨🇦','CAD',1,7,NOW(),NOW()),('AU','Australia','🇦🇺','AUD',1,8,NOW(),NOW()),
('DE','Germany','🇩🇪','EUR',1,9,NOW(),NOW()),('FR','France','🇫🇷','EUR',1,10,NOW(),NOW()),
('IT','Italy','🇮🇹','EUR',1,11,NOW(),NOW()),('ES','Spain','🇪🇸','EUR',1,12,NOW(),NOW()),
('TR','Türkiye','🇹🇷','TRY',1,13,NOW(),NOW()),('MY','Malaysia','🇲🇾','MYR',1,14,NOW(),NOW()),
('SG','Singapore','🇸🇬','SGD',1,15,NOW(),NOW()),('JP','Japan','🇯🇵','JPY',1,16,NOW(),NOW()),
('KR','South Korea','🇰🇷','KRW',1,17,NOW(),NOW()),('IN','India','🇮🇳','INR',1,18,NOW(),NOW()),
('BD','Bangladesh','🇧🇩','BDT',1,19,NOW(),NOW()),('QA','Qatar','🇶🇦','QAR',1,20,NOW(),NOW()),
('KW','Kuwait','🇰🇼','KWD',1,21,NOW(),NOW()),('OM','Oman','🇴🇲','OMR',1,22,NOW(),NOW()),
('BH','Bahrain','🇧🇭','BHD',1,23,NOW(),NOW()),('ZA','South Africa','🇿🇦','ZAR',1,24,NOW(),NOW()),
('NL','Netherlands','🇳🇱','EUR',1,25,NOW(),NOW()),('BE','Belgium','🇧🇪','EUR',1,26,NOW(),NOW()),
('AT','Austria','🇦🇹','EUR',1,27,NOW(),NOW()),('SE','Sweden','🇸🇪','SEK',1,28,NOW(),NOW()),
('NO','Norway','🇳🇴','NOK',1,29,NOW(),NOW()),('DK','Denmark','🇩🇰','DKK',1,30,NOW());
