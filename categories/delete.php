<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /categories/index.php");
    exit;
}

// Try delete
try {
    $del = $pdo->prepare("DELETE FROM categories WHERE id = ? LIMIT 1");
    $del->execute([$id]);

    header("Location: /categories/index.php");
    exit;
} catch (PDOException $e) {
    // Most likely FK constraint fails because items reference this category
    $msg = urlencode("Cannot delete category: it is used by existing items. Please move/delete items first.");
    header("Location: /categories/index.php?error=" . $msg);
    exit;
}
