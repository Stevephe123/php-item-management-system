<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_GET["error"])) {
    $error = $_GET["error"];
}

$error = "";
$success = "";

// Handle Add Category
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");

    if ($name === "") {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            $success = "Category added successfully.";
        } catch (PDOException $e) {
            // Duplicate name (unique constraint) or other DB errors
            $error = "Failed to add category. It may already exist.";
        }
    }
}

// Fetch categories
$stmt = $pdo->query("SELECT id, name, created_at FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categories</title>
    <link rel="stylesheet" href="/css/categories-index.css" />
</head>
<body>
  <div class="container">

    <div class="topbar">
      <a class="btn" href="/items/index.php">← Back to Items</a>
      <div style="display:flex; gap:10px;">
        <a class="btn" href="/auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="card">
      <h1>Categories</h1>

      <form method="POST" style="margin-top:10px; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        <div style="flex:1; min-width:240px;">
          <label>New Category Name *</label>
          <input type="text" name="name" placeholder="e.g. Monitor" required />
        </div>
        <button class="btn primary" type="submit">Add Category</button>
      </form>

      <?php if ($error): ?>
        <div class="msg err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="msg ok"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (count($categories) === 0): ?>
          <tr><td colspan="3">No categories yet.</td></tr>
        <?php else: ?>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td><strong><?= htmlspecialchars($c["name"]) ?></strong></td>
              <td><?= htmlspecialchars($c["created_at"]) ?></td>
              <td>
                <div class="row-actions">
                  <a class="link" href="/categories/edit.php?id=<?= (int)$c["id"] ?>">Edit</a>
                  <a class="link danger" href="/categories/delete.php?id=<?= (int)$c["id"] ?>"
                     onclick="return confirm('Delete this category? If items use it, deletion will fail.');">Delete</a>
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
