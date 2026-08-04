<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Contact Us';
$activeNav = 'contact';
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In production, send an email or store the message in a database table.
    $sent = true;
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a> / Contact</div>
        <h1>Get in Touch</h1>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="form-card">
            <?php if ($sent): ?>
                <div class="alert alert-success">Thanks for reaching out! We'll get back to you within 24 hours.</div>
            <?php endif; ?>
            <form method="POST" action="contact.php">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <div class="form-foot">
                    <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
