<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout.php');
}

$cartData = getCartDetails($pdo);
if (empty($cartData['items'])) {
    redirect('shop.php');
}

$customerName = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$paymentMethod = $_POST['payment_method'] ?? 'cod';

if ($customerName === '' || $email === '' || $phone === '' || $address === '' || $city === '') {
    setFlash('error', 'Please fill in all required fields.');
    redirect('checkout.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Please enter a valid email address.');
    redirect('checkout.php');
}

try {
    $pdo->beginTransaction();

    // Re-check stock at order time to avoid overselling
    foreach ($cartData['items'] as $item) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$item['product']['id']]);
        $currentStock = (int)$stmt->fetchColumn();
        if ($currentStock < $item['quantity']) {
            throw new Exception($item['product']['name'] . ' only has ' . $currentStock . ' left in stock.');
        }
    }

    $orderNumber = generateOrderNumber();
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, email, phone, address, city, notes, payment_method, total_amount, status)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $orderNumber,
        currentUserId(),
        $customerName,
        $email,
        $phone,
        $address,
        $city,
        $notes,
        $paymentMethod,
        $cartData['total'],
    ]);
    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stockStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($cartData['items'] as $item) {
        $p = $item['product'];
        $itemStmt->execute([$orderId, $p['id'], $p['name'], $p['price'], $item['quantity'], $item['subtotal']]);
        $stockStmt->execute([$item['quantity'], $p['id']]);
    }

    $pdo->commit();
    clearCart();
    redirect('order-success.php?order=' . urlencode($orderNumber));

} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('error', 'We could not process your order: ' . $e->getMessage());
    redirect('checkout.php');
}
