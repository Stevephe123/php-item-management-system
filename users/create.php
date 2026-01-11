<?php
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Name, email, and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);

            header("Location: /users/index.php?msg=" . urlencode("User created successfully."));
            exit;
        } catch (PDOException $e) {
            $error = "Failed to create user. Email may already exist.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add User</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .container{max-width:780px; margin:0 auto; padding:24px;}
    .topbar{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px;}
    .btn{display:inline-block; padding:9px 12px; border-radius:10px; text-decoration:none; font-size:14px; border:1px solid #ddd; background:#fff; color:#111;}
    .btn.primary{background:#111; color:#fff; border-color:#111;}
    .card{background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:18px;}
    h1{margin:0; font-size:20px;}
    label{display:block; font-size:13px; margin:12px 0 6px;}
    input{width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none; background:#fff;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
    .muted{color:#666; font-size:13px; margin-top:6px;}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <a class="btn" href="/users/index.php">← Back</a>
      <a class="btn" href="/auth/logout.php">Logout</a>
    </div>

    <div class="card">
      <h1>Add User</h1>
      <div class="muted">Create a new account for system access.</div>

      <form method="POST">
        <label>Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST["name"] ?? "") ?>" required />

        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required />

        <label>Password *</label>
        <input type="password" name="password" placeholder="Minimum 6 characters" required />

        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn primary" type="submit">Create User</button>
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
