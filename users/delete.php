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
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:760px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.danger{background:#b00020; color:#fff; border-color:#b00020;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:18px;}
    h1{margin:0; font-size:20px;}
    .muted{color:#666; font-size:13px; margin-top:6px;}
    .box{margin-top:14px; padding:12px; background:#fafafa; border:1px solid #eee; border-radius:12px;}
    .row{display:flex; gap:10px; margin-top:14px; flex-wrap:wrap;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
  </style>
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
