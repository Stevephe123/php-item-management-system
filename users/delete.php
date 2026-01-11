<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /users/index.php");
    exit;
}

// Prevent deleting yourself
if ($id === (int)($_SESSION["user_id"] ?? 0)) {
    header("Location: /users/index.php?msg=" . urlencode("You cannot delete your own account while logged in."));
    exit;
}

// Load user for confirmation
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: /users/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $posted = (int)($_POST["id"] ?? 0);
    if ($posted !== (int)$user["id"]) {
        $error = "Invalid request.";
    } else {
        $del = $pdo->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
        $del->execute([$user["id"]]);

        header("Location: /users/index.php?msg=" . urlencode("User deleted successfully."));
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Delete User</title>
    <link rel="stylesheet" href="/css/users-delete.css" />
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="btn" href="/users/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Delete User</h1>
      <div class="muted">This action cannot be undone.</div>

      <div class="box">
        <div><strong><?= htmlspecialchars($user["name"]) ?></strong></div>
        <div class="muted"><?= htmlspecialchars($user["email"]) ?></div>
      </div>

      <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$user["id"] ?>" />
        <div class="row">
          <button class="btn danger" type="submit">Yes, delete</button>
          <a class="btn" href="/users/index.php">Cancel</a>
        </div>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
