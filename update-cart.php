<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

$action = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);

if ($action === 'update') {
    $quantity = (int)($_POST['quantity'] ?? 1);
    updateCartItem($productId, $quantity);
    setFlash('success', 'Cart updated.');
} elseif ($action === 'remove') {
    removeFromCart($productId);
    setFlash('success', 'Item removed from cart.');
} elseif ($action === 'clear') {
    clearCart();
    setFlash('success', 'Cart cleared.');
}

redirect('cart.php');
