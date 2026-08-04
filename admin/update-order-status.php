<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        setFlash('success', 'Order status updated.');
    }
    redirect('order-view.php?id=' . $id);
}

redirect('orders.php');
