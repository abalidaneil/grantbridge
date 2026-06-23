<?php
require 'config.php';

function ensureProfileColumns(PDO $pdo): void {
    $columns = $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('phone', $columns, true)) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(30) DEFAULT NULL");
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') === false && $e->getCode() !== '42S21') {
                throw $e;
            }
        }
    }

    if (!in_array('bio', $columns, true)) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL");
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') === false && $e->getCode() !== '42S21') {
                throw $e;
            }
        }
    }
}

ensureProfileColumns($pdo);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($full_name === '' || $email === '') {
        $_SESSION['profile_error'] = 'Full name and email are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$email, $user_id]);

        if ($stmt->fetch()) {
            $_SESSION['profile_error'] = 'That email is already in use.';
        } else {
            $update = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, phone = ?, bio = ? WHERE id = ?');
            $update->execute([$full_name, $email, $phone, $bio, $user_id]);
            $_SESSION['user_name'] = $full_name;
            $_SESSION['profile_success'] = 'Your profile was updated successfully.';
        }
    }

    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare('SELECT full_name, email, phone, bio FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

$profile_error = $_SESSION['profile_error'] ?? '';
$profile_success = $_SESSION['profile_success'] ?? '';
unset($_SESSION['profile_error'], $_SESSION['profile_success']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Profile</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="dashboard-shell">
      <div class="dashboard-card">
        <nav class="dashboard-nav">
          <a class="brand" href="index.html">GrantBridge</a>
          <div class="dashboard-nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="donations.php">Donations</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Log Out</a>
          </div>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Your account</span>
            <h1>Edit profile</h1>
            <p>Update your personal details and bio.</p>
          </div>
        </div>

        <div class="profile-grid">
          <div class="panel">
            <?php if ($profile_error !== ''): ?>
              <div class="message message-error"><?php echo htmlspecialchars($profile_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($profile_success !== ''): ?>
              <div class="message" style="background:#e7f9ee;color:#1d6b3f;"><?php echo htmlspecialchars($profile_success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="profile.php">
              <label>
                Full name
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>" required />
              </label>

              <label>
                Email address
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required />
              </label>

              <label>
                Phone number
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
              </label>

              <label>
                Bio
                <textarea name="bio" rows="5"><?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              </label>

              <button class="btn btn-primary auth-submit" type="submit">Save Profile</button>
            </form>
          </div>

          <div class="panel">
            <h3>Profile tips</h3>
            <p>Keep your contact details updated so grant organizations can reach you easily.</p>
            <div class="list">
              <div class="list-item">Add a professional bio to describe your mission.</div>
              <div class="list-item">Use a valid email for application updates.</div>
              <div class="list-item">Update your phone number if you change devices.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
