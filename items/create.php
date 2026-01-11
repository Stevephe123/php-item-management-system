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
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:780px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:18px;}
    h1{margin:0; font-size:20px;}
    label{display:block; font-size:13px; margin:12px 0 6px;}
    input, select, textarea{width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none; background:#fff;}
    textarea{min-height:110px; resize:vertical;}
    .row{display:grid; grid-template-columns: 1fr 1fr; gap:12px;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
    .muted{color:#666; font-size:13px; margin-top:6px;}
  </style>
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
