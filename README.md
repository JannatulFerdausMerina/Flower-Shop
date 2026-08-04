# Bloom & Petal — Flower Shop (PHP + MySQL)

A full e-commerce flower shop built with vanilla PHP (PDO) and MySQL — product catalog, cart, checkout, customer accounts, and a complete admin panel. No frameworks required.

## Features

**Storefront**
- Home page with hero, categories, and featured products
- Shop page with category filters, search, and pagination
- Product detail pages with related products
- Session-based shopping cart (add / update quantity / remove / clear)
- Guest or logged-in checkout, with order confirmation page
- Customer accounts: register, login, order history

**Admin Panel** (`/admin`)
- Secure login, separate from customer accounts
- Dashboard with revenue, order, product, and customer stats, plus a low-stock alert
- Product management: add / edit / delete, with image upload or image URL
- Category management: add / delete
- Order management: view details, update order status (pending → processing → shipped → delivered / cancelled)

## Requirements

- PHP 8.0+ with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx (or PHP's built-in server for quick local testing)

## Setup

1. **Create the database.** Import the schema and seed data:
   ```bash
   mysql -u root -p < database.sql
   ```
   This creates the `flower_shop` database, all tables, 5 categories, 12 sample products, and a default admin account.

2. **Configure the connection.** Edit `config/database.php` and set your MySQL credentials and site URL:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'flower_shop');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');

   define('SITE_URL', 'http://localhost/flowershop'); // no trailing slash
   ```
   `SITE_URL` should match wherever you place this folder on your server (e.g. if you copy this folder into `htdocs/flowershop`, keep it as `http://localhost/flowershop`).

3. **Set folder permissions** so the admin panel can save uploaded product images:
   ```bash
   chmod -R 755 uploads/
   ```

4. **Open the site.** Visit `SITE_URL` in your browser for the storefront, and `SITE_URL/admin/login.php` for the admin panel.

## Default Admin Login

```
Username: admin
Password: admin123
```
Change this password (or add a new admin and remove this one) before deploying anywhere public — there's no UI for it yet, so update it directly in the `admins` table using `password_hash()` in PHP, e.g.:
```bash
php -r "echo password_hash('your_new_password', PASSWORD_BCRYPT);"
```
then update the `password` column for that row.

## Project Structure

```
flowershop/
├── config/database.php       Database connection + site settings
├── includes/                 Shared functions, header, footer
├── assets/css/style.css      Storefront design system
├── assets/js/main.js         Mobile nav, quantity steppers
├── uploads/products/         Uploaded product images land here
├── admin/                    Admin panel (login, dashboard, CRUD)
├── database.sql              Schema + seed data
├── index.php, shop.php, product.php, cart.php, checkout.php, ...
```

## Notes

- All database queries use PDO prepared statements to prevent SQL injection.
- Passwords (both customer and admin) are hashed with bcrypt via `password_hash()`.
- Stock is re-checked and decremented inside a database transaction at checkout to avoid overselling.
- Sample product images are pulled from Unsplash via URL for demo purposes — replace them with your own photos using the admin panel's image upload field.
- Checkout supports guest orders; logged-in customers get their details pre-filled and can see order history under "My Account."
