<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && $password === $admin["password"]) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_user"] = $admin["username"];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        body { font-family: Arial; background:#f4f4f4; display:flex; height:100vh; align-items:center; justify-content:center; margin:0; }
        .login-box { background:#fff; padding:35px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.15); width:340px; }
        .login-box h2 { text-align:center; margin-bottom:20px; }
        .login-box label { display:block; margin-bottom:6px; font-weight:bold; }
        .login-box input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; }
        .login-box button { width:100%; padding:11px; background:#222; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer; }
        .login-box button:hover { background:#444; }
        .error { color:red; text-align:center; margin-bottom:10px; }
        .back { display:block; text-align:center; margin-top:15px; color:#555; text-decoration:none; }
    </style>
</head>
<body>

<form class="login-box" method="POST">
    <h2>🔐 Admin Login</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <label>Username</label>
    <input type="text" name="username" autofocus required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
    <a href="index.php" class="back">← Back to Site</a>
</form>

</body>
</html>