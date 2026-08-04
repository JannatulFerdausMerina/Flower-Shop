<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Order Confirmed';
$orderNumber = $_GET['order'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="success-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="12" r="10"/><path d="M8 12.5l2.8 2.8L16.5 9"/></svg>
            <h1>Thank you, <?= clean($order['customer_name']) ?>!</h1>
            <p>Your order has been received and is being prepared with care.</p>
            <div class="order-num">Order #<?= clean($order['order_number']) ?></div>
            <p>A confirmation has been noted for <strong><?= clean($order['email']) ?></strong>. Total: <strong><?= formatPrice($order['total_amount']) ?></strong></p>
            <div class="hero-cta" style="justify-content:center; margin-top:30px;">
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                <a href="index.php" class="btn btn-outline">Back to Home</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
