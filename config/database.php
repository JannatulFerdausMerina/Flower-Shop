<?php
/**
 * Database Configuration
 * Bloom & Petal Flower Shop
 *
 * Update the constants below with your MySQL credentials.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'flower_shop');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Bloom & Petal');
define('SITE_URL', 'http://localhost/flowershop'); // change to your actual base URL, no trailing slash
define('SHOP_CURRENCY_SYMBOL', '$');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
