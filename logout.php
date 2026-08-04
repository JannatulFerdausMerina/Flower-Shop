<?php
require_once __DIR__ . '/includes/functions.php';

unset($_SESSION['user_id'], $_SESSION['user_name']);
setFlash('success', 'You have been logged out.');
redirect('index.php');
