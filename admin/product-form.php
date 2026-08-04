<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

$adminActive = 'products';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = ['id'=>null,'category_id'=>'','name'=>'','description'=>'','price'=>'','stock'=>'','image'=>'','featured'=>0];
$errors = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { redirect('products.php'); }
    $product = $found;
}

$pageTitle = $id ? 'Edit Product' : 'Add Product';
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $imageUrl = trim($_POST['image_url'] ?? '');

    if ($name === '') $errors[] = 'Product name is required.';
    if ($categoryId <= 0) $errors[] = 'Please select a category.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';

    // Handle file upload (optional — takes priority over the image URL field if provided)
    $finalImage = $imageUrl ?: $product['image'];
    if (!empty($_FILES['image_file']['name'])) {
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Image must be a JPG, PNG, WEBP, or GIF file.';
        } elseif ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image must be smaller than 5MB.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/products/';
            $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $filename)) {
                $finalImage = 'uploads/products/' . $filename;
            } else {
                $errors[] = 'Failed to upload image. Please try again.';
            }
        }
    }

    if (empty($errors) && $finalImage === '') {
        $errors[] = 'Please provide a product image (upload a file or paste an image URL).';
    }

    if (empty($errors)) {
        $slug = makeSlug($name);
        // Ensure slug uniqueness
        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, $id ?? 0]);
        if ($checkStmt->fetch()) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, stock=?, image=?, featured=? WHERE id=?");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $stock, $finalImage, $featured, $id]);
            setFlash('success', 'Product updated successfully.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, stock, image, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $name, $slug, $description, $price, $stock, $finalImage, $featured]);
            setFlash('success', 'Product added successfully.');
        }
        redirect('products.php');
    } else {
        // repopulate $product with submitted values for re-display
        $product = array_merge($product, [
            'category_id' => $categoryId, 'name' => $name, 'description' => $description,
            'price' => $price, 'stock' => $stock, 'image' => $finalImage, 'featured' => $featured,
        ]);
    }
}

require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-panel">
    <div class="panel-head"><h2><?= $id ? 'Edit Product' : 'Add New Product' ?></h2><a href="products.php" class="btn btn-outline btn-sm">Back to Products</a></div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php foreach ($errors as $e) echo clean($e) . '<br>'; ?></div>
    <?php endif; ?>

    <form method="POST" action="product-form.php<?= $id ? '?id='.$id : '' ?>" enctype="multipart/form-data">
        <div class="admin-form-grid">
            <div>
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required value="<?= clean($product['name']) ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= $product['category_id']==$cat['id']?'selected':'' ?>><?= clean($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (<?= SHOP_CURRENCY_SYMBOL ?>)</label>
                        <input type="number" id="price" step="0.01" min="0.01" name="price" required value="<?= clean($product['price']) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" min="0" name="stock" required value="<?= clean($product['stock']) ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"><?= clean($product['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="featured" value="1" <?= $product['featured'] ? 'checked' : '' ?> style="width:auto; margin-right:8px;">Mark as featured product</label>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label>Current / Preview Image</label>
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= clean($product['image']) ?>" class="current-image" alt="">
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="image_file">Upload New Image</label>
                    <input type="file" id="image_file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif">
                    <p class="helper-text">JPG, PNG, WEBP or GIF. Max 5MB.</p>
                </div>
                <div class="form-group">
                    <label for="image_url">Or Image URL</label>
                    <input type="text" id="image_url" name="image_url" placeholder="https://..." value="<?= (strpos($product['image'] ?? '', 'http') === 0) ? clean($product['image']) : '' ?>">
                    <p class="helper-text">Used only if no file is uploaded above.</p>
                </div>
            </div>
        </div>
        <div class="form-foot">
            <button type="submit" class="btn btn-primary"><?= $id ? 'Update Product' : 'Add Product' ?></button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
