<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

// Fetch items with category name
$sql = "
    SELECT i.id, i.name, i.description, i.quantity, i.created_at,
           c.name AS category_name
    FROM items i
    JOIN categories c ON i.category_id = c.id
    ORDER BY i.created_at DESC
";
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Items Dashboard</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:1100px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .title h1{margin:0; font-size:20px;}
    .title p{margin:4px 0 0; color:#555; font-size:13px;}
    .actions{display:flex; gap:10px; align-items:center;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden;}
    table{width:100%; border-collapse:collapse;}
    th, td{padding:12px 12px; border-bottom:1px solid #eee; text-align:left; vertical-align:top;}
    th{font-size:12px; color:#444; background:#fafafa; letter-spacing:.02em; text-transform:uppercase;}
    td{font-size:14px;}
    .muted{color:#666; font-size:13px;}
    .row-actions{display:flex; gap:10px;}
    .link{color:#111; text-decoration:none; border-bottom:1px dotted #999;}
    .danger{color:#b00020;}
    .empty{padding:18px; color:#555;}
    .badge{display:inline-block; padding:3px 8px; border-radius:999px; background:#f0f0f0; font-size:12px;}
  </style>
</head>
<body>
  <div class="container">

    <div class="topbar">
      <div class="title">
        <h1>Items Dashboard</h1>
        <p>Welcome, <strong><?= htmlspecialchars($_SESSION["user_name"] ?? "User") ?></strong></p>
      </div>

      <div class="actions">
        <a class="btn" href="/categories/index.php">Manage Categories</a>
        <a class="btn primary" href="/items/create.php">+ Add Item</a>
        <a class="btn" href="/auth/logout.php">Logout</a>

      </div>
    </div>

    <div class="card">
      <?php if (count($items) === 0): ?>
        <div class="empty">No items found. Click “Add Item” to create your first record.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><strong><?= htmlspecialchars($item["name"]) ?></strong></td>
              <td><span class="badge"><?= htmlspecialchars($item["category_name"]) ?></span></td>
              <td class="muted"><?= htmlspecialchars($item["description"] ?? "") ?></td>
              <td><?= (int)$item["quantity"] ?></td>
              <td class="muted"><?= htmlspecialchars($item["created_at"]) ?></td>
              <td>
                <div class="row-actions">
                  <a class="link" href="/items/edit.php?id=<?= (int)$item["id"] ?>">Edit</a>
                  <a class="link danger" href="/items/delete.php?id=<?= (int)$item["id"] ?>"
                     onclick="return confirm('Delete this item?');">Delete</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
