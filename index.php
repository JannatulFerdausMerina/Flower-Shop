<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Flowers for Every Moment';
$activeNav = 'home';

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC LIMIT 5")->fetchAll();
$featured = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.featured = 1 ORDER BY p.id DESC LIMIT 8")->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <div class="eyebrow"><?= sprigIcon() ?> Freshly cut, every morning</div>
            <h1>Flowers that say<br>what words can't.</h1>
            <p class="lede">Hand-tied bouquets, garden roses, and thoughtfully arranged gift boxes — gathered each morning and delivered the same day across Dhaka.</p>
            <div class="hero-cta">
                <a href="shop.php" class="btn btn-primary">Shop the Collection</a>
                <a href="shop.php?category=wedding-flowers" class="btn btn-outline">Wedding Flowers</a>
            </div>
        </div>
        <div class="hero-art">
            <span class="hero-sprig top"><?= sprigIcon() ?></span>
            <img src="https://images.unsplash.com/photo-1487070183336-b863922373d4?w=800" alt="Fresh bouquet of flowers">
            <span class="hero-sprig bottom"><?= sprigIcon() ?></span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="eyebrow"><?= sprigIcon() ?> Browse by Category</div>
            <h2>Find Your Perfect Arrangement</h2>
        </div>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="shop.php?category=<?= clean($cat['slug']) ?>" class="category-card">
                <img src="<?= clean($cat['image']) ?>" alt="<?= clean($cat['name']) ?>" loading="lazy">
                <div class="overlay"></div>
                <div class="label"><?= clean($cat['name']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tinted">
    <div class="container">
        <div class="section-header">
            <div class="eyebrow"><?= sprigIcon() ?> Customer Favorites</div>
            <h2>Featured Arrangements</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
            <div class="product-card">
                <a href="product.php?slug=<?= clean($p['slug']) ?>" class="thumb">
                    <?php if ($p['featured']): ?><span class="badge">Popular</span><?php endif; ?>
                    <img src="<?= clean($p['image']) ?>" alt="<?= clean($p['name']) ?>" loading="lazy">
                </a>
                <div class="body">
                    <div class="cat"><?= clean($p['category_name']) ?></div>
                    <h3><a href="product.php?slug=<?= clean($p['slug']) ?>"><?= clean($p['name']) ?></a></h3>
                    <div class="price"><?= formatPrice($p['price']) ?></div>
                    <form action="add-to-cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                        <input type="hidden" name="redirect" value="index.php">
                        <button type="submit" class="btn btn-outline btn-block btn-sm" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                            <?= $p['stock'] < 1 ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center" style="margin-top:50px;">
            <a href="shop.php" class="btn btn-primary">View All Flowers</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php renderDivider(); ?>
        <div class="section-header" style="margin-bottom:0;">
            <h2>Same-Day Delivery, Always Fresh</h2>
            <p style="color:var(--charcoal-60);">Every order is hand-arranged the morning it ships, never held in cold storage for days. We work directly with local growers around Dhaka to keep every stem at its best.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
