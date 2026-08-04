<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Orders';
$adminActive = 'orders';

$statusFilter = $_GET['status'] ?? '';
$statuses = ['pending','processing','shipped','delivered','cancelled'];

if (in_array($statusFilter, $statuses, true)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$statusFilter]);
} else {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel">
    <div class="panel-head">
        <h2>All Orders (<?= count($orders) ?>)</h2>
        <div class="filter-pills">
            <a href="orders.php" class="pill <?= $statusFilter==='' ? 'active' : '' ?>">All</a>
            <?php foreach ($statuses as $s): ?>
                <a href="orders.php?status=<?= $s ?>" class="pill <?= $statusFilter===$s ? 'active' : '' ?>"><?= ucfirst($s) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="admin-table-wrap">
    <?php if (empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= clean($o['order_number']) ?></td>
                <td><?= clean($o['customer_name']) ?></td>
                <td><?= clean($o['phone']) ?></td>
                <td><?= formatPrice($o['total_amount']) ?></td>
                <td><span class="badge-status badge-<?= clean($o['status']) ?>"><?= clean($o['status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                <td><a href="order-view.php?id=<?= (int)$o['id'] ?>" class="edit">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
