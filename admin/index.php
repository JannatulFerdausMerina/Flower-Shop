<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';
$adminActive = 'dashboard';

$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCustomers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6")->fetchAll();
$lowStock = $pdo->query("SELECT name, stock FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <div><div class="label">Total Revenue</div><div class="num"><?= formatPrice($totalRevenue) ?></div></div>
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
    </div>
    <div class="stat-card">
        <div><div class="label">Orders</div><div class="num"><?= $totalOrders ?></div></div>
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l2.6 12.6a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="9" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg></div>
    </div>
    <div class="stat-card">
        <div><div class="label">Products</div><div class="num"><?= $totalProducts ?></div></div>
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
    </div>
    <div class="stat-card">
        <div><div class="label">Customers</div><div class="num"><?= $totalCustomers ?></div></div>
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg></div>
    </div>
</div>

<div class="admin-form-grid">
    <div class="admin-panel">
        <div class="panel-head">
            <h2>Recent Orders</h2>
            <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="admin-table-wrap">
        <?php if (empty($recentOrders)): ?>
            <p>No orders yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td><a href="order-view.php?id=<?= (int)$o['id'] ?>"><?= clean($o['order_number']) ?></a></td>
                        <td><?= clean($o['customer_name']) ?></td>
                        <td><?= formatPrice($o['total_amount']) ?></td>
                        <td><span class="badge-status badge-<?= clean($o['status']) ?>"><?= clean($o['status']) ?></span></td>
                        <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>
    </div>

    <div class="admin-panel">
        <div class="panel-head"><h2>Low Stock Alert</h2></div>
        <?php if (empty($lowStock)): ?>
            <p>All products are well stocked.</p>
        <?php else: ?>
            <?php foreach ($lowStock as $p): ?>
                <div class="flex-between" style="padding:10px 0; border-bottom:1px solid var(--border);">
                    <span><?= clean($p['name']) ?></span>
                    <span class="badge-status badge-cancelled"><?= (int)$p['stock'] ?> left</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
