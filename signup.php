<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handled in register.php
    header('Location: register.php');
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
    <title>GrantBridge | Sign Up</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="auth-body">
    <div class="auth-shell">
      <div class="auth-card">
        <a class="brand auth-brand" href="index.html">GrantBridge</a>
        <h1>Create your account</h1>
        <p>Start exploring grants and manage your applications in one place.</p>

        <?php if ($error !== ''): ?>
          <div class="message message-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form class="auth-form" action="register.php" method="post">
          <label>
            Full name
            <input type="text" name="full_name" placeholder="Jane Doe" required />
          </label>

          <label>
            Email address
            <input type="email" name="email" placeholder="you@example.com" required />
          </label>

          <label>
            Password
            <input type="password" name="password" placeholder="Create a password" required />
          </label>

          <label>
            Confirm password
            <input type="password" name="confirm_password" placeholder="Confirm your password" required />
          </label>

          <button class="btn btn-primary auth-submit" type="submit">Create Account</button>
        </form>

        <p class="auth-switch">
          Already have an account?
          <a href="login.php">Log in</a>
        </p>
      </div>
    </div>
  </body>
</html>
