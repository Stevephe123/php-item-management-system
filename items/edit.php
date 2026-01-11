<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

// Get item id
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /items/index.php");
    exit;
}

// Load categories
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Load existing item
$itemStmt = $pdo->prepare("SELECT id, name, description, category_id, quantity FROM items WHERE id = ? LIMIT 1");
$itemStmt->execute([$id]);
$item = $itemStmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: /items/index.php");
    exit;
}

// If POST, update item
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category_id = (int)($_POST["category_id"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? -1);

    if ($name === "") {
        $error = "Item name is required.";
    } elseif ($category_id <= 0) {
        $error = "Please select a category.";
    } elseif ($quantity < 0) {
        $error = "Quantity must be 0 or more.";
    } else {
        // Ensure category exists
        $check = $pdo->prepare("SELECT id FROM categories WHERE id = ? LIMIT 1");
        $check->execute([$category_id]);
        if (!$check->fetch()) {
            $error = "Invalid category selected.";
        } else {
            $upd = $pdo->prepare("
                UPDATE items
                SET name = ?, description = ?, category_id = ?, quantity = ?
                WHERE id = ?
            ");
            $upd->execute([$name, $description, $category_id, $quantity, $id]);

            header("Location: /items/index.php");
            exit;
        }
    }

    // keep form filled with submitted values if error
    $item["name"] = $name;
    $item["description"] = $description;
    $item["category_id"] = $category_id;
    $item["quantity"] = $quantity;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Item</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:780px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .btn.danger{border-color:#f2c7cd; color:#b00020; background:#fff;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:18px;}
    h1{margin:0; font-size:20px;}
    label{display:block; font-size:13px; margin:12px 0 6px;}
    input, select, textarea{width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none; background:#fff;}
    textarea{min-height:110px; resize:vertical;}
    .row{display:grid; grid-template-columns: 1fr 1fr; gap:12px;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
    .muted{color:#666; font-size:13px; margin-top:6px;}
    .actions{margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;}
    .danger-note{font-size:12px; color:#666; margin-top:12px;}
  </style>
</head>
<body>
  <div class="container">

    <div class="topbar">
      <a class="btn" href="/items/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Edit Item</h1>
      <div class="muted">Update item details (ID: <?= (int)$item["id"] ?>).</div>

      <form method="POST">
        <label>Item Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($item["name"]) ?>" required />

        <div class="row">
          <div>
            <label>Category *</label>
            <select name="category_id" required>
              <option value="">-- Select category --</option>
              <?php foreach ($categories as $c): ?>
                <?php $selected = ((int)$item["category_id"] === (int)$c["id"]) ? "selected" : ""; ?>
                <option value="<?= (int)$c["id"] ?>" <?= $selected ?>>
                  <?= htmlspecialchars($c["name"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label>Quantity *</label>
            <input type="number" name="quantity" min="0" value="<?= (int)$item["quantity"] ?>" required />
          </div>
        </div>

        <label>Description</label>
        <textarea name="description" placeholder="Optional"><?= htmlspecialchars($item["description"] ?? "") ?></textarea>

        <div class="actions">
          <button class="btn primary" type="submit">Update Item</button>
          <a class="btn" href="/items/index.php">Cancel</a>
          <a class="btn danger" href="/items/delete.php?id=<?= (int)$item["id"] ?>"
             onclick="return confirm('Delete this item?');">Delete</a>
        </div>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="danger-note">Tip: Use the Delete button only if you’re sure.</div>
    </div>

  </div>
</body>
</html>
