<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$application_error = $_SESSION['application_error'] ?? '';
$application_success = $_SESSION['application_success'] ?? '';
unset($_SESSION['application_error'], $_SESSION['application_success']);

$stmt = $pdo->query('SELECT id, title, category, amount FROM grant_opportunities ORDER BY id ASC');
$grants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Apply for Grant</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="dashboard-shell">
      <div class="dashboard-card">
        <nav class="dashboard-nav">
          <a class="brand" href="index.html">GrantBridge</a>
          <div class="dashboard-nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="apply_grant.php">Apply for Grant</a>
            <a href="donations.php">Donations</a>
            <a href="profile.php">Profile</a>
          </div>
          <a class="btn btn-secondary" href="logout.php">Log Out</a>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Request funding</span>
            <h1>Apply for a grant</h1>
            <p>Hello <?php echo $user_name; ?>, complete the form below to request funding for your project or program.</p>
          </div>
        </div>

        <div class="donation-grid">
          <div class="panel">
            <h3>Grant application form</h3>
            <?php if ($application_error !== ''): ?>
              <div class="message message-error"><?php echo htmlspecialchars($application_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($application_success !== ''): ?>
              <div class="message" style="background:#e7f9ee;color:#1d6b3f;"><?php echo htmlspecialchars($application_success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form class="auth-form donation-form" action="process_grant_application.php" method="post" enctype="multipart/form-data">
              <label>
                Amount requested
                <input type="number" name="amount" placeholder="5000" min="1" step="0.01" required />
              </label>

              <label>
                Grant type
                <select name="grant_id" required>
                  <option value="">Select a grant opportunity</option>
                  <?php foreach ($grants as $grant): ?>
                    <option value="<?php echo (int) $grant['id']; ?>">
                      <?php echo htmlspecialchars($grant['title'] . ' - ' . $grant['category'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>

              <label>
                Supporting document (optional)
                <input type="file" name="document" accept="image/png,image/jpeg,image/jpg,image/webp" />
              </label>
              <p class="helper-text">Optional government document image. Maximum size is 2MB.</p>

              <label>
                Notes
                <textarea name="notes" rows="4" placeholder="Describe your request and how the funding will be used."></textarea>
              </label>

              <button class="btn btn-primary auth-submit" type="submit">Submit Application</button>
            </form>
          </div>

          <div class="panel">
            <h3>What happens next</h3>
            <ul class="hero-highlights">
              <li>Your request will be reviewed by the grant team.</li>
              <li>We will contact you if we need additional information.</li>
              <li>Optional supporting documents help speed up the review process.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
