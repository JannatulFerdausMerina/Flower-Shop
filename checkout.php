<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Checkout';
$cartData = getCartDetails($pdo);

if (empty($cartData['items'])) {
    setFlash('info', 'Your cart is empty. Add some flowers before checking out.');
    redirect('shop.php');
}

// Pre-fill from logged-in user if available
$prefill = ['name' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => ''];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
    $stmt->execute([currentUserId()]);
    if ($u = $stmt->fetch()) {
        $prefill['name'] = $u['name'];
        $prefill['email'] = $u['email'];
        $prefill['phone'] = $u['phone'] ?? '';
        $prefill['address'] = $u['address'] ?? '';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / <a href="cart.php">Cart</a> / Checkout</div>
        <h1>Checkout</h1>
    </div>
</div>

<section class="section">
    <div class="container cart-layout">
        <div class="form-card wide" style="margin:0;">
            <h3>Shipping Details</h3>
            <form action="place-order.php" method="POST">
                <div class="form-group">
                    <label for="customer_name">Full Name</label>
                    <input type="text" id="customer_name" name="customer_name" required value="<?= clean($prefill['name']) ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?= clean($prefill['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required value="<?= clean($prefill['phone']) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" required><?= clean($prefill['address']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required value="<?= clean($prefill['city']) ?>" placeholder="e.g. Dhaka">
                </div>
                <div class="form-group">
                    <label for="notes">Order Notes (optional)</label>
                    <textarea id="notes" name="notes" placeholder="Delivery instructions, gift message, etc."></textarea>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method">
                        <option value="cod">Cash on Delivery</option>
                        <option value="card">Credit / Debit Card</option>
                        <option value="bkash">bKash</option>
                    </select>
                </div>
                <div class="form-foot">
                    <button type="submit" class="btn btn-primary btn-block">Place Order — <?= formatPrice($cartData['total']) ?></button>
                </div>
            </form>
        </div>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            <?php foreach ($cartData['items'] as $item): ?>
                <div class="summary-row">
                    <span><?= clean($item['product']['name']) ?> &times; <?= (int)$item['quantity'] ?></span>
                    <span><?= formatPrice($item['subtotal']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row total"><span>Total</span><span><?= formatPrice($cartData['total']) ?></span></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
