<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            // session login
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: /items/index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
    <link rel="stylesheet" href="/css/auth-login.css" />
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Sign in</h1>
      <p>Login to manage items.</p>

      <form method="POST" autocomplete="off">
        <label>Email</label>
        <input type="email" name="email" placeholder="admin@example.com" required />

        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required />

        <button class="btn" type="submit">Login</button>
      </form>

      <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="hint">
        Demo: admin@example.com / admin123
      </div>
    </div>
  </div>
</body>
</html>
