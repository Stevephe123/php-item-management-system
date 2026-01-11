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

// Load item (for confirmation display)
$stmt = $pdo->prepare("
    SELECT i.id, i.name, i.description, i.quantity, c.name AS category_name
    FROM items i
    JOIN categories c ON i.category_id = c.id
    WHERE i.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: /items/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Safety: confirm posted id matches
    $postedId = (int)($_POST["id"] ?? 0);
    if ($postedId !== (int)$item["id"]) {
        $error = "Invalid request.";
    } else {
        $del = $pdo->prepare("DELETE FROM items WHERE id = ? LIMIT 1");
        $del->execute([$item["id"]]);

        header("Location: /items/index.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Delete Item</title>
    <link rel="stylesheet" href="/css/items-delete.css" />
</head>
<body>
  <div class="container">

    <div class="topbar">
      <a class="btn" href="/items/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Delete Item</h1>
      <div class="muted">This action cannot be undone.</div>

      <div class="box">
        <div><strong><?= htmlspecialchars($item["name"]) ?></strong></div>
        <div class="muted">
          Category: <span class="badge"><?= htmlspecialchars($item["category_name"]) ?></span>
          · Quantity: <?= (int)$item["quantity"] ?>
        </div>
        <?php if (!empty($item["description"])): ?>
          <div class="muted" style="margin-top:8px;">
            <?= htmlspecialchars($item["description"]) ?>
          </div>
        <?php endif; ?>
      </div>

      <form method="POST">
        <input type="hidden" name="id" value="<?= (int)$item["id"] ?>" />
        <div class="row">
          <button class="btn danger" type="submit">Yes, delete</button>
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
