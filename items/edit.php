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
    <link rel="stylesheet" href="/css/items-edit.css" />
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
