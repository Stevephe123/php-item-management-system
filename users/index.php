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
    <link rel="stylesheet" href="/css/users-index.css" />
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
