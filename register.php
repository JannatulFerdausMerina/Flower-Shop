<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Create Account';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $phone]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        setFlash('success', 'Welcome to Bloom & Petal, ' . $name . '!');
        redirect('account.php');
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / Create Account</div>
        <h1>Create Account</h1>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error"><?php foreach ($errors as $e) echo clean($e) . '<br>'; ?></div>
            <?php endif; ?>
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required value="<?= clean($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= clean($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= clean($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-foot">
                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
                </div>
            </form>
            <p class="text-center" style="margin-top:18px;">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
