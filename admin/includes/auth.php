<?php
/**
 * Include this at the very top of any protected admin page,
 * after requiring config/database.php.
 */
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}
