<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: /users/index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: /users/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $newPassword = $_POST["new_password"] ?? "";

    if ($name === "" || $email === "") {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($newPassword !== "" && strlen($newPassword) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {
        try {
            if ($newPassword !== "") {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $upd = $pdo->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?");
                $upd->execute([$name, $email, $hash, $id]);
            } else {
                $upd = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?");
                $upd->execute([$name, $email, $id]);
            }

            header("Location: /users/index.php?msg=" . urlencode("User updated successfully."));
            exit;
        } catch (PDOException $e) {
            $error = "Failed to update user. Email may already exist.";
        }
    }

    $user["name"] = $name;
    $user["email"] = $email;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit User</title>
    <link rel="stylesheet" href="/css/users-edit.css" />
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="btn" href="/users/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Edit User</h1>
      <div class="muted">Update user details. Leave password blank to keep current password.</div>

      <form method="POST">
        <label>Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user["name"]) ?>" required />

        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required />

        <label>New Password (optional)</label>
        <input type="password" name="new_password" placeholder="Leave empty to keep current password" />

        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn primary" type="submit">Save</button>
          <a class="btn" href="/users/index.php">Cancel</a>
        </div>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
