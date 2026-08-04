<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('shop.php');
}

$productId = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);
$redirectTo = $_POST['redirect'] ?? 'shop.php';

$stmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'That product could not be found.');
    redirect($redirectTo);
}

if ($product['stock'] < 1) {
    setFlash('error', $product['name'] . ' is currently out of stock.');
    redirect($redirectTo);
}

$quantity = max(1, min($quantity, $product['stock']));
addToCart($productId, $quantity);
setFlash('success', $product['name'] . ' was added to your cart.');
redirect($redirectTo);
