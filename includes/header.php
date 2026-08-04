<?php
/**
 * Site Header
 * Expects $pdo to be available, and optionally $pageTitle / $activeNav set by the including page.
 */
if (!isset($pdo)) { require_once __DIR__ . '/../config/database.php'; }
require_once __DIR__ . '/functions.php';

$cartCount = getCartCount();
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? clean($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' — Flowers for every moment'; ?></title>
<meta name="description" content="Bloom & Petal — hand-tied bouquets, roses, wedding flowers and gift boxes, delivered with care.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="<?= SITE_URL ?>/index.php" class="logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C12 2 8 5 8 9C8 11.2 9.8 13 12 13C14.2 13 16 11.2 16 9C16 5 12 2 12 2Z" fill="currentColor" opacity="0.85"/><path d="M12 13C12 13 5 14 3 19C3 19 9 21 12 17C15 21 21 19 21 19C19 14 12 13 12 13Z" fill="currentColor" opacity="0.6"/><path d="M12 13V22" stroke="currentColor" stroke-width="1.3"/></svg>
            Bloom<span>&amp;</span>Petal
        </a>

        <nav class="main-nav" id="mainNav">
            <a href="<?= SITE_URL ?>/index.php" class="<?= $activeNav==='home'?'active':'' ?>">Home</a>
            <a href="<?= SITE_URL ?>/shop.php" class="<?= $activeNav==='shop'?'active':'' ?>">Shop</a>
            <a href="<?= SITE_URL ?>/shop.php?category=wedding-flowers" class="<?= $activeNav==='wedding'?'active':'' ?>">Weddings</a>
            <a href="<?= SITE_URL ?>/contact.php" class="<?= $activeNav==='contact'?'active':'' ?>">Contact</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= SITE_URL ?>/account.php" class="<?= $activeNav==='account'?'active':'' ?>">My Account</a>
                <a href="<?= SITE_URL ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= SITE_URL ?>/login.php" class="<?= $activeNav==='login'?'active':'' ?>">Login</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions">
            <a href="<?= SITE_URL ?>/cart.php" class="icon-link" aria-label="Cart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l2.6 12.6a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="9" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>
</header>

<?php $flash = getFlash(); if ($flash): ?>
<div class="container" style="margin-top:24px;">
    <div class="alert alert-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
</div>
<?php endif; ?>
