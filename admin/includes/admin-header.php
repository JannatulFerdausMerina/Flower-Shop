<?php
/**
 * Admin Header — expects $pdo, $pageTitle, and optionally $adminActive to be set.
 */
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/auth.php';
$adminActive = $adminActive ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($pageTitle ?? 'Admin') ?> | <?= SITE_NAME ?> Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C12 2 8 5 8 9C8 11.2 9.8 13 12 13C14.2 13 16 11.2 16 9C16 5 12 2 12 2Z" fill="currentColor"/></svg>
            Bloom Admin
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="<?= $adminActive==='dashboard'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Dashboard
            </a>
            <div class="section-label">Catalog</div>
            <a href="products.php" class="<?= $adminActive==='products'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
            </a>
            <a href="categories.php" class="<?= $adminActive==='categories'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                Categories
            </a>
            <div class="section-label">Sales</div>
            <a href="orders.php" class="<?= $adminActive==='orders'?'active':'' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l2.6 12.6a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="9" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                Orders
            </a>
            <div class="section-label">&nbsp;</div>
            <a href="<?= SITE_URL ?>/index.php" target="_blank">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>
                View Site
            </a>
            <a href="logout.php">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                Logout
            </a>
        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1><?= clean($pageTitle ?? 'Dashboard') ?></h1>
            <div class="admin-user"><?= clean($_SESSION['admin_name'] ?? 'Admin') ?></div>
        </div>
        <div class="admin-content">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
        <?php endif; ?>
