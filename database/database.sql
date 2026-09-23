-- Chacha Prime Marketplace single manual MySQL schema.
-- No Laravel migration files.
SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE IF NOT EXISTS cache (`key` VARCHAR(255) PRIMARY KEY, `value` MEDIUMTEXT NOT NULL, expiration INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS cache_locks (`key` VARCHAR(255) PRIMARY KEY, owner VARCHAR(255) NOT NULL, expiration INT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS jobs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, queue VARCHAR(255) NOT NULL, payload LONGTEXT NOT NULL, attempts TINYINT UNSIGNED NOT NULL, reserved_at INT UNSIGNED NULL, available_at INT UNSIGNED NOT NULL, created_at INT UNSIGNED NOT NULL, INDEX(`queue`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS job_batches (id VARCHAR(255) PRIMARY KEY, name VARCHAR(255) NOT NULL, total_jobs INT NOT NULL, pending_jobs INT NOT NULL, failed_jobs INT NOT NULL, failed_job_ids LONGTEXT NOT NULL, options MEDIUMTEXT NULL, cancelled_at INT NULL, created_at INT NOT NULL, finished_at INT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS failed_jobs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid VARCHAR(255) UNIQUE NOT NULL, connection TEXT NOT NULL, queue TEXT NOT NULL, payload LONGTEXT NOT NULL, exception LONGTEXT NOT NULL, failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(255) PRIMARY KEY, user_id BIGINT UNSIGNED NULL, ip_address VARCHAR(45), user_agent TEXT, payload LONGTEXT NOT NULL, last_activity INT NOT NULL, INDEX sessions_user_id_index (user_id), INDEX sessions_last_activity_index (last_activity)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,remember_token VARCHAR(100) NULL,role ENUM('super_admin','admin','staff','vendor','b2b_customer','customer','affiliate','courier','streamer') NOT NULL DEFAULT 'customer',status ENUM('active','pending','suspended','blocked') NOT NULL DEFAULT 'pending',email_verified_at TIMESTAMP NULL,two_factor_enabled TINYINT(1) NOT NULL DEFAULT 1,totp_secret TEXT NULL,phone VARCHAR(40),avatar VARCHAR(500),locale VARCHAR(10) DEFAULT 'en',created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,INDEX(role,status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS roles (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) UNIQUE NOT NULL,description VARCHAR(255),created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS permissions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) UNIQUE NOT NULL,description VARCHAR(255),created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS role_permissions (role_id BIGINT UNSIGNED NOT NULL,permission_id BIGINT UNSIGNED NOT NULL,PRIMARY KEY(role_id,permission_id),FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE,FOREIGN KEY(permission_id) REFERENCES permissions(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS vendors (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL UNIQUE,business_name VARCHAR(180) NOT NULL,legal_name VARCHAR(180),country VARCHAR(100),registration_number VARCHAR(120),tax_number VARCHAR(120),status ENUM('pending','approved','rejected','suspended') DEFAULT 'pending',verification_status ENUM('pending','verified','rejected') DEFAULT 'pending',logo VARCHAR(500),description TEXT,commission_rate DECIMAL(6,3) DEFAULT 10,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,INDEX(status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS addresses (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,label VARCHAR(60),recipient_name VARCHAR(120) NOT NULL,phone VARCHAR(40),country VARCHAR(100) NOT NULL,state VARCHAR(120),city VARCHAR(120),postal_code VARCHAR(30),address_line1 VARCHAR(255) NOT NULL,address_line2 VARCHAR(255),is_default TINYINT(1) DEFAULT 0,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS categories (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,parent_id BIGINT UNSIGNED NULL,name VARCHAR(150) NOT NULL,slug VARCHAR(180) UNIQUE NOT NULL,description TEXT,image VARCHAR(500),sort_order INT DEFAULT 0,status TINYINT(1) DEFAULT 1,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(parent_id) REFERENCES categories(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS brands (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,slug VARCHAR(180) UNIQUE NOT NULL,logo VARCHAR(500),status TINYINT(1) DEFAULT 1,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS products (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,vendor_id BIGINT UNSIGNED NOT NULL,category_id BIGINT UNSIGNED NULL,brand_id BIGINT UNSIGNED NULL,name VARCHAR(220) NOT NULL,slug VARCHAR(240) UNIQUE NOT NULL,sku VARCHAR(100) UNIQUE NOT NULL,description LONGTEXT,short_description TEXT,highlights LONGTEXT NULL,specifications LONGTEXT NULL,shipping_info LONGTEXT NULL,features LONGTEXT NULL,additional_details LONGTEXT NULL,return_policy VARCHAR(500) NULL DEFAULT '30 days refund / replacement',gift_option TINYINT(1) DEFAULT 1,fulfillment_type ENUM('seller','marketplace') DEFAULT 'seller',sold_by_type ENUM('seller','marketplace_fba') DEFAULT 'seller',video_urls LONGTEXT NULL,retail_price DECIMAL(18,2) DEFAULT 0,wholesale_price DECIMAL(18,2) DEFAULT 0,factory_price DECIMAL(18,2) DEFAULT 0,cost_price DECIMAL(18,2) DEFAULT 0,currency CHAR(3) DEFAULT 'USD',stock DECIMAL(18,3) DEFAULT 0,stock_status ENUM('in_stock','out_of_stock','backorder') DEFAULT 'in_stock',status ENUM('draft','pending','published','rejected','archived') DEFAULT 'draft',featured TINYINT(1) DEFAULT 0,weight DECIMAL(12,3),created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(vendor_id) REFERENCES vendors(id),FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL,FOREIGN KEY(brand_id) REFERENCES brands(id) ON DELETE SET NULL,INDEX(vendor_id,status),INDEX(category_id,status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS product_images (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,path VARCHAR(500) NOT NULL,alt_text VARCHAR(255),sort_order INT DEFAULT 0,is_primary TINYINT(1) DEFAULT 0,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS product_variants (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,sku VARCHAR(100) UNIQUE NOT NULL,name VARCHAR(180) NOT NULL,attributes JSON,price DECIMAL(18,2) NOT NULL,stock DECIMAL(18,3) DEFAULT 0,status TINYINT(1) DEFAULT 1,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS product_videos (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,path VARCHAR(500) NOT NULL,title VARCHAR(190) NULL,sort_order INT DEFAULT 0,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS pricing_tiers (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,customer_type ENUM('b2b','b2c') NOT NULL,min_quantity DECIMAL(18,3) DEFAULT 1,max_quantity DECIMAL(18,3),price DECIMAL(18,2) NOT NULL,discount_percent DECIMAL(7,3) DEFAULT 0,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS carts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL UNIQUE,currency CHAR(3) DEFAULT 'USD',created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS cart_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,cart_id BIGINT UNSIGNED NOT NULL,product_id BIGINT UNSIGNED NOT NULL,variant_id BIGINT UNSIGNED NULL,quantity DECIMAL(18,3) NOT NULL,unit_price DECIMAL(18,2) NOT NULL,FOREIGN KEY(cart_id) REFERENCES carts(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id),FOREIGN KEY(variant_id) REFERENCES product_variants(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS orders (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,order_number VARCHAR(40) UNIQUE NOT NULL,status VARCHAR(30) DEFAULT 'pending',payment_status VARCHAR(30) DEFAULT 'unpaid',fulfillment_status VARCHAR(30) DEFAULT 'unfulfilled',currency CHAR(3) DEFAULT 'USD',subtotal DECIMAL(18,2) DEFAULT 0,discount_total DECIMAL(18,2) DEFAULT 0,shipping_total DECIMAL(18,2) DEFAULT 0,tax_total DECIMAL(18,2) DEFAULT 0,grand_total DECIMAL(18,2) DEFAULT 0,shipping_address JSON,billing_address JSON,notes TEXT,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id),INDEX(user_id,status)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS order_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,order_id BIGINT UNSIGNED NOT NULL,vendor_id BIGINT UNSIGNED NOT NULL,product_id BIGINT UNSIGNED NOT NULL,variant_id BIGINT UNSIGNED NULL,product_name VARCHAR(220) NOT NULL,sku VARCHAR(100),quantity DECIMAL(18,3) NOT NULL,unit_price DECIMAL(18,2) NOT NULL,subtotal DECIMAL(18,2) NOT NULL,vendor_status VARCHAR(30) DEFAULT 'pending',FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,FOREIGN KEY(vendor_id) REFERENCES vendors(id),FOREIGN KEY(product_id) REFERENCES products(id),FOREIGN KEY(variant_id) REFERENCES product_variants(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payment_methods (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code VARCHAR(80) UNIQUE NOT NULL,name VARCHAR(120) NOT NULL,type VARCHAR(30) NOT NULL,enabled TINYINT(1) DEFAULT 0,settings JSON,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payments (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,order_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,method_id BIGINT UNSIGNED NOT NULL,transaction_reference VARCHAR(180),amount DECIMAL(18,2) NOT NULL,currency CHAR(3) DEFAULT 'USD',status VARCHAR(30) DEFAULT 'pending',gateway_response JSON,paid_at TIMESTAMP NULL,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(order_id) REFERENCES orders(id),FOREIGN KEY(user_id) REFERENCES users(id),FOREIGN KEY(method_id) REFERENCES payment_methods(id),INDEX(transaction_reference)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS wallets (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL UNIQUE,currency CHAR(3) DEFAULT 'USD',balance DECIMAL(18,2) DEFAULT 0,status VARCHAR(20) DEFAULT 'active',created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS wallet_transactions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,wallet_id BIGINT UNSIGNED NOT NULL,type VARCHAR(30) NOT NULL,amount DECIMAL(18,2) NOT NULL,balance_after DECIMAL(18,2) NOT NULL,reference_type VARCHAR(80),reference_id BIGINT UNSIGNED,description VARCHAR(255),created_at TIMESTAMP NULL,FOREIGN KEY(wallet_id) REFERENCES wallets(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS virtual_cards (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL UNIQUE,card_token CHAR(64) UNIQUE NOT NULL,display_number VARCHAR(25) NOT NULL,last4 CHAR(4) NOT NULL,expiry_month TINYINT UNSIGNED NOT NULL,expiry_year SMALLINT UNSIGNED NOT NULL,status VARCHAR(20) DEFAULT 'active',issued_at TIMESTAMP NULL,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS group_buying_campaigns (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,title VARCHAR(220) NOT NULL,target_participants INT UNSIGNED NOT NULL,offer_price DECIMAL(18,2) NOT NULL,currency CHAR(3) DEFAULT 'USD',starts_at TIMESTAMP NOT NULL,ends_at TIMESTAMP NOT NULL,status VARCHAR(30) DEFAULT 'draft',created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(product_id) REFERENCES products(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS group_buying_participants (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,campaign_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,order_id BIGINT UNSIGNED NULL,quantity DECIMAL(18,3) DEFAULT 1,status VARCHAR(30) DEFAULT 'reserved',joined_at TIMESTAMP NULL,FOREIGN KEY(campaign_id) REFERENCES group_buying_campaigns(id) ON DELETE CASCADE,FOREIGN KEY(user_id) REFERENCES users(id),FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE SET NULL,UNIQUE KEY uq_group_user(campaign_id,user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS live_streams (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,vendor_id BIGINT UNSIGNED NOT NULL,title VARCHAR(220) NOT NULL,youtube_url VARCHAR(500) NOT NULL,youtube_video_id VARCHAR(100),status VARCHAR(30) DEFAULT 'scheduled',viewer_count INT UNSIGNED DEFAULT 0,starts_at TIMESTAMP NULL,ended_at TIMESTAMP NULL,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(vendor_id) REFERENCES vendors(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS live_products (live_stream_id BIGINT UNSIGNED NOT NULL,product_id BIGINT UNSIGNED NOT NULL,pin_order INT DEFAULT 0,PRIMARY KEY(live_stream_id,product_id),FOREIGN KEY(live_stream_id) REFERENCES live_streams(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS live_messages (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,live_stream_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,message TEXT NOT NULL,status VARCHAR(30) DEFAULT 'visible',created_at TIMESTAMP NULL,FOREIGN KEY(live_stream_id) REFERENCES live_streams(id) ON DELETE CASCADE,FOREIGN KEY(user_id) REFERENCES users(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS reviews (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,product_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,order_id BIGINT UNSIGNED NULL,rating TINYINT UNSIGNED NOT NULL,title VARCHAR(180),body TEXT,status VARCHAR(30) DEFAULT 'pending',created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,FOREIGN KEY(user_id) REFERENCES users(id),FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS wishlists (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,product_id BIGINT UNSIGNED NOT NULL,created_at TIMESTAMP NULL,UNIQUE KEY uq_wishlist(user_id,product_id),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS coupons (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,vendor_id BIGINT UNSIGNED NULL,code VARCHAR(80) UNIQUE NOT NULL,type VARCHAR(20) NOT NULL,value DECIMAL(18,2) NOT NULL,min_order DECIMAL(18,2) DEFAULT 0,max_discount DECIMAL(18,2),starts_at TIMESTAMP NULL,ends_at TIMESTAMP NULL,usage_limit INT UNSIGNED,used_count INT UNSIGNED DEFAULT 0,status TINYINT(1) DEFAULT 1,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(vendor_id) REFERENCES vendors(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS shipping_methods (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code VARCHAR(80) UNIQUE NOT NULL,name VARCHAR(120) NOT NULL,provider VARCHAR(100),mode VARCHAR(20) DEFAULT 'manual',enabled TINYINT(1) DEFAULT 0,settings JSON,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS shipments (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,order_id BIGINT UNSIGNED NOT NULL,vendor_id BIGINT UNSIGNED NOT NULL,shipping_method_id BIGINT UNSIGNED NOT NULL,tracking_number VARCHAR(150),status VARCHAR(30) DEFAULT 'pending',shipping_cost DECIMAL(18,2) DEFAULT 0,shipped_at TIMESTAMP NULL,delivered_at TIMESTAMP NULL,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL,FOREIGN KEY(order_id) REFERENCES orders(id),FOREIGN KEY(vendor_id) REFERENCES vendors(id),FOREIGN KEY(shipping_method_id) REFERENCES shipping_methods(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS currencies (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code CHAR(3) UNIQUE NOT NULL,name VARCHAR(80) NOT NULL,symbol VARCHAR(10),rate_to_base DECIMAL(24,10) DEFAULT 1,source VARCHAR(20) DEFAULT 'manual',enabled TINYINT(1) DEFAULT 1,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS settings (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,setting_key VARCHAR(190) UNIQUE NOT NULL,setting_value LONGTEXT, value_type VARCHAR(30) DEFAULT 'string',is_public TINYINT(1) DEFAULT 0,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS otp_codes (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,purpose VARCHAR(50) NOT NULL,code_hash VARCHAR(255) NOT NULL,expires_at TIMESTAMP NOT NULL,consumed_at TIMESTAMP NULL,attempts TINYINT UNSIGNED DEFAULT 0,created_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS notifications (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NOT NULL,type VARCHAR(120) NOT NULL,title VARCHAR(180) NOT NULL,message TEXT NOT NULL,data JSON,read_at TIMESTAMP NULL,created_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS ai_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NULL,context VARCHAR(40) NOT NULL,provider VARCHAR(40) DEFAULT 'gemini',model VARCHAR(100),prompt LONGTEXT,response LONGTEXT,tokens_used INT UNSIGNED,created_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vendor_payouts (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 vendor_id BIGINT UNSIGNED NOT NULL,
 wallet_id BIGINT UNSIGNED NULL,
 amount DECIMAL(18,2) NOT NULL,
 currency VARCHAR(3) NOT NULL DEFAULT 'USD',
 method VARCHAR(60) NULL,
 destination VARCHAR(190) NULL,
 status ENUM('requested','processing','paid','rejected','cancelled') NOT NULL DEFAULT 'requested',
 notes TEXT NULL,
 processed_by BIGINT UNSIGNED NULL,
 processed_at TIMESTAMP NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 INDEX idx_vendor_payouts_vendor_status (vendor_id,status),
 CONSTRAINT fk_vendor_payouts_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
 CONSTRAINT fk_vendor_payouts_wallet FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE SET NULL,
 CONSTRAINT fk_vendor_payouts_processor FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS customer_messages (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 vendor_id BIGINT UNSIGNED NOT NULL,
 customer_id BIGINT UNSIGNED NOT NULL,
 order_id BIGINT UNSIGNED NULL,
 subject VARCHAR(190) NULL,
 body TEXT NOT NULL,
 sender_role ENUM('customer','vendor') NOT NULL,
 status ENUM('open','read','closed') NOT NULL DEFAULT 'open',
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 INDEX idx_customer_messages_vendor_status (vendor_id,status),
 INDEX idx_customer_messages_thread (vendor_id,customer_id,order_id),
 CONSTRAINT fk_customer_messages_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
 CONSTRAINT fk_customer_messages_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_customer_messages_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS return_requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id BIGINT UNSIGNED NOT NULL,
 order_item_id BIGINT UNSIGNED NULL,
 vendor_id BIGINT UNSIGNED NOT NULL,
 customer_id BIGINT UNSIGNED NOT NULL,
 reason VARCHAR(190) NOT NULL,
 details TEXT NULL,
 refund_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
 currency VARCHAR(3) NOT NULL DEFAULT 'USD',
 status ENUM('requested','approved','rejected','received','refunded','cancelled') NOT NULL DEFAULT 'requested',
 resolution_note TEXT NULL,
 processed_at TIMESTAMP NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 INDEX idx_return_requests_vendor_status (vendor_id,status),
 INDEX idx_return_requests_customer (customer_id,status),
 CONSTRAINT fk_return_requests_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_return_requests_item FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE SET NULL,
 CONSTRAINT fk_return_requests_vendor FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
 CONSTRAINT fk_return_requests_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT UNSIGNED NULL,action VARCHAR(120) NOT NULL,entity_type VARCHAR(120),entity_id BIGINT UNSIGNED,ip_address VARCHAR(45),user_agent TEXT,metadata JSON,created_at TIMESTAMP NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS pages (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,slug VARCHAR(180) UNIQUE NOT NULL,title VARCHAR(220) NOT NULL,content LONGTEXT,status TINYINT(1) DEFAULT 1,created_at TIMESTAMP NULL,updated_at TIMESTAMP NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO roles(name,description) VALUES ('super_admin','Full platform control'),('admin','Administrative staff'),('staff','Staff member'),('vendor','Marketplace seller'),('b2b_customer','Business buyer'),('customer','Retail customer'),('affiliate','Affiliate partner'),('courier','Delivery staff'),('streamer','Live commerce streamer');
INSERT IGNORE INTO permissions(name,description) VALUES
('dashboard.view','View admin dashboard'),
('users.view','View users'),
('users.manage','Manage users'),
('vendors.manage','Manage vendors'),
('products.manage','Manage products'),
('categories.manage','Manage categories'),
('orders.view','View orders'),
('orders.manage','Manage orders'),
('payments.view','View payments'),
('payments.manage','Manage payments'),
('wallets.manage','Manage wallets'),
('cards.manage','Manage virtual cards'),
('staff.manage','Manage staff'),
('permissions.manage','Manage role permissions'),
('modules.manage','Manage platform modules'),
('audit.view','View audit logs');

INSERT IGNORE INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r CROSS JOIN permissions p WHERE r.name='staff' AND p.name IN
('dashboard.view','users.view','vendors.manage','products.manage','categories.manage','orders.view','payments.view','modules.manage','audit.view');

INSERT IGNORE INTO currencies(code,name,symbol,rate_to_base,source) VALUES ('USD','US Dollar','$',1,'manual'),('PKR','Pakistani Rupee','Rs',1,'manual'),('CNY','Chinese Yuan','¥',1,'manual'),('AED','UAE Dirham','د.إ',1,'manual'),('EUR','Euro','€',1,'manual');
INSERT IGNORE INTO payment_methods(code,name,type,enabled) VALUES ('pingpong','PingPong','gateway',0),('lianlianpay','LianLianPay','gateway',0),('worldfirst','WorldFirst','gateway',0),('alipay','Alipay','gateway',0),('wechat_pay','WeChat Pay','gateway',0),('visa','Visa','gateway',0),('mastercard','Mastercard','gateway',0),('amex','American Express','gateway',0),('paypal','PayPal','gateway',0),('easypaisa','Easypaisa','gateway',0),('jazzcash','JazzCash','gateway',0),('raast','Raast','gateway',0),('bank_transfer','Bank Transfer','bank',0),('cod','Cash on Delivery','cod',0);
INSERT IGNORE INTO shipping_methods(code,name,provider,mode,enabled) VALUES ('fedex','FedEx','FedEx','api',0),('ups','UPS','UPS','api',0),('usps','USPS','USPS','api',0),('dhl','DHL','DHL','api',0),('tcs','TCS','TCS','api',0),('leopards','Leopards','Leopards','api',0),('postex','PostEx','PostEx','api',0),('sf_express','SF Express','SF Express','api',0),('jt','J&T','J&T','api',0),('manual','Manual Shipping','Internal','manual',1);
SET FOREIGN_KEY_CHECKS=1;
