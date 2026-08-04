<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Login';
$activeNav = 'login';
$error = '';

if (isLoggedIn()) {
    redirect('account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('account.php');
    } else {
        $error = 'Invalid email or password.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / Login</div>
        <h1>Welcome Back</h1>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="form-card">
            <?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= clean($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-foot">
                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                </div>
            </form>
            <p class="text-center" style="margin-top:18px;">Don't have an account? <a href="register.php">Create one</a></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
