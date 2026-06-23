<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim(strtolower($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        $full_name === "" ||
        $email === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {
        $_SESSION["admin_error"] = "Please complete all fields.";
        header("Location: create_admin.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["admin_error"] = "Please enter a valid email address.";
        header("Location: create_admin.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION["admin_error"] = "Passwords do not match.";
        header("Location: create_admin.php");
        exit();
    }

    $check = $pdo->prepare("SELECT id FROM admin_accounts WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $_SESSION["admin_error"] =
            "An admin account with that email already exists.";
        header("Location: create_admin.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare(
        "INSERT INTO admin_accounts (full_name, email, password_hash) VALUES (?, ?, ?)",
    );
    $insert->execute([$full_name, $email, $password_hash]);

    $_SESSION["admin_success"] = "Admin account created successfully.";
    header("Location: admin.php");
    exit();
}

$admin_error = $_SESSION["admin_error"] ?? "";
$admin_success = $_SESSION["admin_success"] ?? "";
unset($_SESSION["admin_error"], $_SESSION["admin_success"]);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Create Admin</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="dashboard-shell">
      <div class="dashboard-card">
        <nav class="dashboard-nav">
          <a class="brand" href="index.html">GrantBridge</a>
          <div class="dashboard-nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="admin.php">Admin</a>
            <a href="logout.php">Log Out</a>
          </div>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Admin setup</span>
            <h1>Create admin account</h1>
            <p>Create a separate admin sign-in that can review and approve user actions.</p>
          </div>
        </div>

        <?php if ($admin_error !== ""): ?>
          <div class="message message-error"><?php echo htmlspecialchars(
              $admin_error,
              ENT_QUOTES,
              "UTF-8",
          ); ?></div>
        <?php endif; ?>
        <?php if ($admin_success !== ""): ?>
          <div class="message" style="background:#e7f9ee;color:#1d6b3f;"><?php echo htmlspecialchars(
              $admin_success,
              ENT_QUOTES,
              "UTF-8",
          ); ?></div>
        <?php endif; ?>

        <div class="panel">
          <form class="auth-form" method="post" action="create_admin.php">
            <label>
              Full name
              <input type="text" name="full_name" required />
            </label>

            <label>
              Email address
              <input type="email" name="email" required />
            </label>

            <label>
              Password
              <input type="password" name="password" required />
            </label>

            <label>
              Confirm password
              <input type="password" name="confirm_password" required />
            </label>

            <button class="btn btn-primary auth-submit" type="submit">Create Admin Account</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
