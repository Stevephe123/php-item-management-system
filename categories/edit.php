<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /categories/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$cat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cat) {
    header("Location: /categories/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    if ($name === "") {
        $error = "Category name is required.";
    } else {
        try {
            $upd = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $upd->execute([$name, $id]);
            header("Location: /categories/index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update category. Name may already exist.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Category</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:720px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:18px;}
    h1{margin:0; font-size:20px;}
    label{display:block; font-size:13px; margin:12px 0 6px;}
    input{width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="btn" href="/categories/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Edit Category</h1>

      <form method="POST">
        <label>Category Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($cat["name"]) ?>" required />

        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn primary" type="submit">Save</button>
          <a class="btn" href="/categories/index.php">Cancel</a>
        </div>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
