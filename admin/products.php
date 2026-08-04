<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Products';
$adminActive = 'products';

$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel">
    <div class="panel-head">
        <h2>All Products (<?= count($products) ?>)</h2>
        <a href="product-form.php" class="btn btn-primary btn-sm">+ Add Product</a>
    </div>
    <div class="admin-table-wrap">
    <?php if (empty($products)): ?>
        <p>No products yet. <a href="product-form.php">Add your first product</a>.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?= clean($p['image']) ?>" class="table-thumb" alt=""></td>
                <td><?= clean($p['name']) ?></td>
                <td><?= clean($p['category_name']) ?></td>
                <td><?= formatPrice($p['price']) ?></td>
                <td><?= (int)$p['stock'] ?></td>
                <td><?= $p['featured'] ? 'Yes' : '—' ?></td>
                <td>
                    <div class="action-links">
                        <a href="product-form.php?id=<?= (int)$p['id'] ?>" class="edit">Edit</a>
                        <form action="delete-product.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="delete" data-confirm="Delete this product? This cannot be undone.">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
