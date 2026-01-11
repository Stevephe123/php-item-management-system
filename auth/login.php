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
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f5f6f7;}
    .wrap{min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;}
    .card{width:100%; max-width:420px; background:#fff; border-radius:14px; padding:22px; box-shadow:0 10px 30px rgba(0,0,0,.08);}
    h1{margin:0 0 6px; font-size:20px;}
    p{margin:0 0 14px; color:#555; font-size:14px;}
    label{display:block; font-size:13px; margin:10px 0 6px;}
    input{width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; outline:none;}
    input:focus{border-color:#bbb;}
    .btn{margin-top:14px; width:100%; padding:10px 12px; border:0; border-radius:10px; background:#111; color:#fff; font-size:14px; cursor:pointer;}
    .err{margin-top:10px; color:#b00020; font-size:13px;}
    .hint{margin-top:10px; color:#666; font-size:12px;}
  </style>
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
