<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $_SESSION['auth_error'] = 'Please enter your email and password.';
        header('Location: login.php');
        exit;
    }

    $admin_stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM admin_accounts WHERE email = ?');
    $admin_stmt->execute([$email]);
    $admin = $admin_stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['user_id'] = (int) $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        $_SESSION['user_role'] = 'admin';
        $_SESSION['admin_id'] = (int) $admin['id'];
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, full_name, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        unset($_SESSION['admin_id']);
        header('Location: dashboard.php');
        exit;
    }

    $_SESSION['auth_error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit;
}

$error = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Login</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="auth-body">
    <div class="auth-shell">
      <div class="auth-card">
        <a class="brand auth-brand" href="index.html">GrantBridge</a>
        <h1>Welcome back</h1>
        <p>Access your dashboard, applications, and funding updates.</p>

        <?php if ($error !== ''): ?>
          <div class="message message-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form class="auth-form" action="login.php" method="post">
          <label>
            Email address
            <input type="email" name="email" placeholder="you@example.com" required />
          </label>

          <label>
            Password
            <input type="password" name="password" placeholder="Enter your password" required />
          </label>

          <div class="form-row">
            <label class="checkbox-row">
              <input type="checkbox" />
              <span>Remember me</span>
            </label>
            <a href="#">Forgot password?</a>
          </div>

          <button class="btn btn-primary auth-submit" type="submit">Log In</button>
        </form>

        <p class="auth-switch">
          Don’t have an account?
          <a href="signup.php">Create one</a>
        </p>
      </div>
    </div>
  </body>
</html>
