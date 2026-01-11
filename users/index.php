<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = $_GET["msg"] ?? "";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Users</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:1000px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden;}
    table{width:100%; border-collapse:collapse;}
    th, td{padding:12px 12px; border-bottom:1px solid #eee; text-align:left;}
    th{font-size:12px; color:#444; background:#fafafa; letter-spacing:.02em; text-transform:uppercase;}
    td{font-size:14px;}
    .row-actions{display:flex; gap:10px; align-items:center;}
    .link{color:#111; text-decoration:none; border-bottom:1px dotted #999;}
    .danger{color:#b00020;}
    .msg{padding:12px; background:#f0fff4; border-bottom:1px solid #e6f6ea; color:#0b6b2f; font-size:13px;}
    .muted{color:#666; font-size:13px;}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="btn" href="/items/index.php">← Back to Items</a>
      <div style="display:flex; gap:10px;">
        <a class="btn primary" href="/users/create.php">+ Add User</a>
        <a class="btn" href="/auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="card">
      <?php if ($flash): ?>
        <div class="msg"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (count($users) === 0): ?>
          <tr><td colspan="5" class="muted">No users found.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= (int)$u["id"] ?></td>
              <td><strong><?= htmlspecialchars($u["name"]) ?></strong></td>
              <td><?= htmlspecialchars($u["email"]) ?></td>
              <td class="muted"><?= htmlspecialchars($u["created_at"]) ?></td>
              <td>
                <div class="row-actions">
                  <a class="link" href="/users/edit.php?id=<?= (int)$u["id"] ?>">Edit</a>
                  <?php if ((int)$u["id"] !== (int)($_SESSION["user_id"] ?? 0)): ?>
                    <a class="link danger" href="/users/delete.php?id=<?= (int)$u["id"] ?>">Delete</a>
                  <?php else: ?>
                    <span class="muted">Current</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
