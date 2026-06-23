<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
$donation_error = $_SESSION['donation_error'] ?? '';
$donation_success = $_SESSION['donation_success'] ?? '';
unset($_SESSION['donation_error'], $_SESSION['donation_success']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Make a Donation</title>
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
            <a href="logout.php">Log Out</a>
          </div>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Support a cause</span>
            <h1>Make a donation</h1>
            <p>Hello <?php echo $user_name; ?>, choose a contribution amount and complete your payment securely.</p>
          </div>
        </div>

        <div class="donation-grid">
          <div class="panel">
            <h3>Donation form</h3>
            <?php if ($donation_error !== ''): ?>
              <div class="message message-error"><?php echo htmlspecialchars($donation_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($donation_success !== ''): ?>
              <div class="message" style="background:#e7f9ee;color:#1d6b3f;"><?php echo htmlspecialchars($donation_success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form class="auth-form donation-form" action="process_donation.php" method="post">
              <label>
                Amount
                <input type="number" name="amount" placeholder="100" min="1" step="0.01" required />
              </label>

              <label>
                Currency
                <select name="currency" required>
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="GBP">GBP</option>
                  <option value="BTC">BTC</option>
                </select>
              </label>

              <label>
                Note
                <textarea name="note" rows="4" placeholder="Optional message for the grant campaign"></textarea>
              </label>

              <button class="btn btn-primary auth-submit" type="submit">Submit Donation</button>
            </form>
          </div>

          <div class="panel">
            <h3>Bitcoin payment</h3>
            <p>To donate with Bitcoin, send your payment to the wallet address below.</p>
            <div class="bitcoin-box">
              <span class="bitcoin-label">BTC Address</span>
              <strong>bc1qxyz123abc456def789ghi012jkl345mno678pqr</strong>
            </div>
            <p class="bitcoin-note">Please include your email address in the transaction note so we can confirm your contribution.</p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
