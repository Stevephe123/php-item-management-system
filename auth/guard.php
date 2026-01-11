<?php
// auth/guard.php for protecting pages that require authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}
