<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shop All Flowers';
$activeNav = 'shop';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

$categorySlug = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($categorySlug !== '') {
    $where[] = 'c.slug = ?';
    $params[] = $categorySlug;
}
if ($search !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON c.id = p.category_id $whereSql");
$countStmt->execute($params);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalProducts / $perPage));

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p JOIN categories c ON c.id = p.category_id
        $whereSql
        ORDER BY p.created_at DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$activeCategoryName = 'All Flowers';
foreach ($categories as $c) {
    if ($c['slug'] === $categorySlug) { $activeCategoryName = $c['name']; break; }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / Shop</div>
        <h1><?= clean($activeCategoryName) ?></h1>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <div class="filter-pills">
                <a href="shop.php" class="pill <?= $categorySlug==='' ? 'active' : '' ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                <a href="shop.php?category=<?= clean($cat['slug']) ?>" class="pill <?= $categorySlug===$cat['slug'] ? 'active' : '' ?>"><?= clean($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
            <form class="search-box" method="GET" action="shop.php">
                <?php if ($categorySlug): ?><input type="hidden" name="category" value="<?= clean($categorySlug) ?>"><?php endif; ?>
                <input type="text" name="search" placeholder="Search flowers..." value="<?= clean($search) ?>">
                <button type="submit" aria-label="Search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                </button>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>No flowers found</h3>
                <p>Try a different search term or browse another category.</p>
                <a href="shop.php" class="btn btn-outline">Browse All Flowers</a>
            </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
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
                        <input type="hidden" name="redirect" value="shop.php<?= $categorySlug ? '?category='.urlencode($categorySlug) : '' ?>">
                        <button type="submit" class="btn btn-outline btn-block btn-sm" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                            <?= $p['stock'] < 1 ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                    $qs = $_GET; $qs['page'] = $i;
                    $url = 'shop.php?' . http_build_query($qs);
                ?>
                <?php if ($i === $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= clean($url) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
