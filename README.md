# Chacha Prime Marketplace

Premium global multi-vendor e-commerce marketplace built with **Laravel 13**, PHP 8.3+, MySQL, HTML, CSS and JavaScript.

## Architecture

- B2C + B2B marketplace
- Multi-vendor seller onboarding
- Tiered wholesale and retail discounts
- Group buying
- Live commerce with embedded YouTube live streams
- Internal closed-loop marketplace virtual balance/card system
- Gemini-powered AI assistants
- Multi-provider payment and shipping abstractions
- Email OTP + TOTP authentication
- English / Urdu / Chinese localization
- Admin, vendor and customer dashboards
- Premium responsive storefront
- Single manual MySQL schema: `database/database.sql`
- No Laravel migration files

## Setup

1. Copy `.env.example` to `.env`.
2. Add your own credentials and API keys.
3. Run `composer install`.
4. Run `php artisan key:generate`.
5. Import `database/database.sql` through MySQL/phpMyAdmin.
6. Run `npm install` and `npm run build`.
7. Point the web server document root to `public/`.

Never commit real passwords, API keys, SMTP credentials or payment secrets.
