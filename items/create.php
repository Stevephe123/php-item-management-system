<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

// Load categories for dropdown
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

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
            $stmt = $pdo->prepare("
                INSERT INTO items (name, description, category_id, quantity)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $category_id, $quantity]);

            header("Location: /items/index.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Item</title>
    <link rel="stylesheet" href="/css/items-create.css" />
</head>
<body>
  <div class="container">

    <div class="topbar">
      <a class="btn" href="/items/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Add Item</h1>
      <div class="muted">Create a new inventory item.</div>

      <form method="POST">
        <label>Item Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST["name"] ?? "") ?>" required />

        <div class="row">
          <div>
            <label>Category *</label>
            <select name="category_id" required>
              <option value="">-- Select category --</option>
              <?php foreach ($categories as $c): ?>
                <?php $selected = ((int)($_POST["category_id"] ?? 0) === (int)$c["id"]) ? "selected" : ""; ?>
                <option value="<?= (int)$c["id"] ?>" <?= $selected ?>>
                  <?= htmlspecialchars($c["name"]) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label>Quantity *</label>
            <input type="number" name="quantity" min="0" value="<?= htmlspecialchars($_POST["quantity"] ?? "0") ?>" required />
          </div>
        </div>

        <label>Description</label>
        <textarea name="description" placeholder="Optional"><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

        <div style="margin-top:14px;">
          <button class="btn primary" type="submit">Save Item</button>
          <a class="btn" href="/items/index.php">Cancel</a>
        </div>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
