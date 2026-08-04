<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Your Cart';
$cartData = getCartDetails($pdo);

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / Cart</div>
        <h1>Your Cart</h1>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($cartData['items'])): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M3 4h2l2.6 12.6a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L22 8H6"/><circle cx="9" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any flowers yet.</p>
                <a href="shop.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
        <div class="cart-layout">
            <div>
                <table class="cart-table">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cartData['items'] as $item): $p = $item['product']; ?>
                        <tr>
                            <td>
                                <div class="cart-item-info">
                                    <img src="<?= clean($p['image']) ?>" alt="<?= clean($p['name']) ?>">
                                    <div>
                                        <h4><a href="product.php?slug=<?= clean($p['slug']) ?>"><?= clean($p['name']) ?></a></h4>
                                    </div>
                                </div>
                            </td>
                            <td><?= formatPrice($p['price']) ?></td>
                            <td>
                                <form class="cart-qty-form" action="update-cart.php" method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$p['stock'] ?>">
                                    <button type="submit" class="btn btn-outline btn-sm">Update</button>
                                </form>
                            </td>
                            <td><?= formatPrice($item['subtotal']) ?></td>
                            <td>
                                <form action="update-cart.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                    <button type="submit" class="remove-link" style="background:none;border:none;cursor:pointer;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <form action="update-cart.php" method="POST" style="margin-top:20px;">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-outline btn-sm">Clear Cart</button>
                </form>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($cartData['total']) ?></span></div>
                <div class="summary-row"><span>Delivery</span><span>Calculated at checkout</span></div>
                <div class="summary-row total"><span>Total</span><span><?= formatPrice($cartData['total']) ?></span></div>
                <a href="checkout.php" class="btn btn-primary btn-block" style="margin-top:20px;">Proceed to Checkout</a>
                <a href="shop.php" class="btn btn-outline btn-block" style="margin-top:12px;">Continue Shopping</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
