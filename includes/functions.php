<?php
/**
 * Helper Functions
 * Bloom & Petal Flower Shop
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Sanitize a string for safe output */
function clean($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Redirect helper */
function redirect($path) {
    header("Location: $path");
    exit;
}

/** Format a price with the site currency symbol */
function formatPrice($amount) {
    return SHOP_CURRENCY_SYMBOL . number_format((float)$amount, 2);
}

/** Create a URL-friendly slug from a string */
function makeSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

/** Generate a unique order number */
function generateOrderNumber() {
    return 'BP' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}

/* ---------------------- Customer auth ---------------------- */

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/* ----------------------- Admin auth ------------------------- */

function isAdminLoggedIn() {
    return !empty($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect('login.php');
    }
}

/* -------------------------- Cart ----------------------------- */
/** Cart is stored in session as [product_id => quantity] */

function getCart() {
    return $_SESSION['cart'] ?? [];
}

function getCartCount() {
    $cart = getCart();
    return array_sum($cart);
}

function addToCart($productId, $qty = 1) {
    $productId = (int)$productId;
    $qty = max(1, (int)$qty);
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }
    $_SESSION['cart'][$productId] += $qty;
}

function updateCartItem($productId, $qty) {
    $productId = (int)$productId;
    $qty = (int)$qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function removeFromCart($productId) {
    unset($_SESSION['cart'][(int)$productId]);
}

function clearCart() {
    $_SESSION['cart'] = [];
}

/** Fetch full cart data (joined with product info) and the total */
function getCartDetails(PDO $pdo) {
    $cart = getCart();
    $items = [];
    $total = 0.0;

    if (!empty($cart)) {
        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $qty = $cart[$product['id']];
            $subtotal = $product['price'] * $qty;
            $total += $subtotal;
            $items[] = [
                'product'  => $product,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];
        }
    }

    return ['items' => $items, 'total' => $total];
}

/** The recurring botanical sprig motif used as a section divider / signature element */
function sprigIcon() {
    return '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.4">
        <path d="M20 36V14"/>
        <path d="M20 14C20 14 11 13 9 5C9 5 18 4 20 14Z"/>
        <path d="M20 18C20 18 29 17 31 9C31 9 22 8 20 18Z"/>
        <path d="M20 24C20 24 12 25 10 31"/>
        <path d="M20 24C20 24 28 25 30 31"/>
    </svg>';
}

function renderDivider() {
    echo '<div class="divider"><span class="line"></span>' . sprigIcon() . '<span class="line"></span></div>';
}

/** Flash messages */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
