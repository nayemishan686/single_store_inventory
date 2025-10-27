# Single Inventory — Laravel + Vue (Vite)

A Single-Store Inventory & Billing System built with **Laravel 10**, **Sanctum**, **Inertia**, **Vue 3**, **TailwindCSS**, **Chart.js**, and **DomPDF**.

This system helps manage products, customers, stock tracking, and invoice generation — all inside one SPA (Single Page Application) dashboard.

---

## Requirements
- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8+ (or MariaDB)

---

## Setup Instructions

from git bash or terminal:

```bash
git clone https://github.com/nayemishan686/single_store_inventory.git
cd single-inventory


cp .env.example .env
# Update DB credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
# Everything else can remain same as .env.example

composer install
php artisan key:generate

php artisan migrate --seed
# Demo user:
# Email: admin@example.com
# Password: password
# In login panel there’s a button named "Use Demo User" — click it to autofill credentials.

npm install
npm run dev
php artisan serve

# Visit the app:
# http://127.0.0.1:8000

# If you face any problem, please mail or call me:
# Email: nayemishan377@gmail.com
# Phone: 01637654490