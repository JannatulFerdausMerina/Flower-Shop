<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pageTitle = 'My Account';
$activeNav = 'account';

$stmt = $pdo->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
$stmt->execute([currentUserId()]);
$user = $stmt->fetch();

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([currentUserId()]);
$orders = $ordersStmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / My Account</div>
        <h1>My Account</h1>
    </div>
</div>

<section class="section">
    <div class="container account-layout">
        <nav class="account-nav">
            <a href="account.php" class="active">Order History</a>
            <a href="shop.php">Continue Shopping</a>
            <a href="logout.php">Logout</a>
        </nav>
        <div>
            <div class="form-card wide" style="margin-bottom:30px;">
                <h3>Account Details</h3>
                <p><strong><?= clean($user['name']) ?></strong><br>
                <?= clean($user['email']) ?><?php if ($user['phone']): ?><br><?= clean($user['phone']) ?><?php endif; ?></p>
            </div>

            <h3>Your Orders</h3>
            <?php if (empty($orders)): ?>
                <p>You haven't placed any orders yet. <a href="shop.php">Start shopping</a>.</p>
            <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><?= clean($o['order_number']) ?></td>
                        <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                        <td><?= formatPrice($o['total_amount']) ?></td>
                        <td><span class="badge-status badge-<?= clean($o['status']) ?>"><?= clean($o['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
