<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Categories';
$adminActive = 'categories';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } else {
        $slug = makeSlug($name);
        $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) {
            $errors[] = 'A category with this name already exists.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $image ?: null]);
            setFlash('success', 'Category added.');
            redirect('categories.php');
        }
    }
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY c.name ASC")->fetchAll();

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-form-grid">
    <div class="admin-panel">
        <div class="panel-head"><h2>All Categories (<?= count($categories) ?>)</h2></div>
        <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th></th><th>Name</th><th>Products</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php if ($cat['image']): ?><img src="<?= clean($cat['image']) ?>" class="table-thumb" alt=""><?php endif; ?></td>
                    <td><?= clean($cat['name']) ?></td>
                    <td><?= (int)$cat['product_count'] ?></td>
                    <td>
                        <div class="action-links">
                            <form action="delete-category.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                <button type="submit" class="delete" data-confirm="Delete this category? Products inside it will also be deleted.">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <div class="admin-panel">
        <h2 style="margin-bottom:20px;">Add Category</h2>
        <?php if (!empty($errors)): ?><div class="alert alert-error"><?php foreach ($errors as $e) echo clean($e).'<br>'; ?></div><?php endif; ?>
        <form method="POST" action="categories.php">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="image">Image URL (optional)</label>
                <input type="text" id="image" name="image" placeholder="https://...">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Add Category</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
