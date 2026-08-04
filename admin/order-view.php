<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) { redirect('orders.php'); }

$pageTitle = 'Order ' . $order['order_number'];
$adminActive = 'orders';

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

$statuses = ['pending','processing','shipped','delivered','cancelled'];

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-form-grid">
    <div class="admin-panel">
        <div class="panel-head"><h2>Order <?= clean($order['order_number']) ?></h2><a href="orders.php" class="btn btn-outline btn-sm">Back</a></div>
        <table class="admin-table">
            <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= clean($it['product_name']) ?></td>
                    <td><?= formatPrice($it['price']) ?></td>
                    <td><?= (int)$it['quantity'] ?></td>
                    <td><?= formatPrice($it['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary-row total" style="margin-top:14px;"><span>Total</span><span><?= formatPrice($order['total_amount']) ?></span></div>

        <?php if ($order['notes']): ?>
            <div style="margin-top:20px;"><strong>Order Notes:</strong><p><?= clean($order['notes']) ?></p></div>
        <?php endif; ?>
    </div>

    <div>
        <div class="admin-panel">
            <h2 style="margin-bottom:16px;">Customer Details</h2>
            <p><strong><?= clean($order['customer_name']) ?></strong><br>
            <?= clean($order['email']) ?><br>
            <?= clean($order['phone']) ?></p>
            <p><?= clean($order['address']) ?>, <?= clean($order['city']) ?></p>
            <p>Payment: <strong><?= clean(ucfirst($order['payment_method'])) ?></strong></p>
            <p>Placed: <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
        </div>

        <div class="admin-panel">
            <h2 style="margin-bottom:16px;">Update Status</h2>
            <form action="update-order-status.php" method="POST">
                <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                <div class="form-group">
                    <select name="status">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status']===$s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Status</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
