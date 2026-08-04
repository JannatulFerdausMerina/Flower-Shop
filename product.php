<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ?");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section text-center"><h2>Product not found</h2><p><a href="shop.php" class="btn btn-primary">Back to Shop</a></p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'];
$activeNav = 'shop';

$relStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a> / <a href="shop.php">Shop</a> /
            <a href="shop.php?category=<?= clean($product['category_slug']) ?>"><?= clean($product['category_name']) ?></a> /
            <?= clean($product['name']) ?>
        </div>
    </div>
</div>

<section class="section">
    <div class="container product-detail">
        <div class="gallery">
            <img src="<?= clean($product['image']) ?>" alt="<?= clean($product['name']) ?>">
        </div>
        <div class="info">
            <div class="cat-tag"><?= clean($product['category_name']) ?></div>
            <h1><?= clean($product['name']) ?></h1>
            <div class="price"><?= formatPrice($product['price']) ?></div>

            <?php if ($product['stock'] > 0): ?>
                <p class="stock in">In stock — <?= (int)$product['stock'] ?> available</p>
            <?php else: ?>
                <p class="stock out">Currently out of stock</p>
            <?php endif; ?>

            <p><?= nl2br(clean($product['description'])) ?></p>

            <?php if ($product['stock'] > 0): ?>
            <form action="add-to-cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <input type="hidden" name="redirect" value="product.php?slug=<?= clean($product['slug']) ?>">
                <div class="qty-row">
                    <label class="mb-0" style="margin:0;">Qty:</label>
                    <button type="button" class="btn btn-outline btn-sm" data-qty-step="-1" data-target="qtyInput">&minus;</button>
                    <input type="number" id="qtyInput" name="quantity" class="qty-input" value="1" min="1" max="<?= (int)$product['stock'] ?>">
                    <button type="button" class="btn btn-outline btn-sm" data-qty-step="1" data-target="qtyInput">&plus;</button>
                </div>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
            <?php else: ?>
                <button class="btn btn-outline" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($related)): ?>
<section class="section section-tinted">
    <div class="container">
        <div class="section-header">
            <div class="eyebrow"><?= sprigIcon() ?> You Might Also Like</div>
            <h2>More from <?= clean($product['category_name']) ?></h2>
        </div>
        <div class="product-grid">
            <?php foreach ($related as $p): ?>
            <div class="product-card">
                <a href="product.php?slug=<?= clean($p['slug']) ?>" class="thumb">
                    <img src="<?= clean($p['image']) ?>" alt="<?= clean($p['name']) ?>" loading="lazy">
                </a>
                <div class="body">
                    <h3><a href="product.php?slug=<?= clean($p['slug']) ?>"><?= clean($p['name']) ?></a></h3>
                    <div class="price"><?= formatPrice($p['price']) ?></div>
                    <form action="add-to-cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                        <input type="hidden" name="redirect" value="product.php?slug=<?= clean($product['slug']) ?>">
                        <button type="submit" class="btn btn-outline btn-block btn-sm" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                            <?= $p['stock'] < 1 ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
