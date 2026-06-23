<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');

$withdrawal_error = $_SESSION['withdrawal_error'] ?? '';
$withdrawal_success = $_SESSION['withdrawal_success'] ?? '';
unset($_SESSION['withdrawal_error'], $_SESSION['withdrawal_success']);

$stmt = $pdo->prepare('SELECT amount, bank_name, account_holder, account_number, status, created_at FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([$user_id]);
$withdrawals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Withdrawal Request</title>
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
            <a href="withdrawal.php">Withdrawal</a>
            <a href="donations.php">Donations</a>
            <a href="profile.php">Profile</a>
          </div>
          <a class="btn btn-secondary" href="logout.php">Log Out</a>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Request payout</span>
            <h1>Withdrawal request</h1>
            <p>Hello <?php echo $user_name; ?>, submit your payout details below. Your request will be reviewed by the admin for processing.</p>
          </div>
        </div>

        <div class="donation-grid">
          <div class="panel">
            <h3>Submit a withdrawal request</h3>
            <?php if ($withdrawal_error !== ''): ?>
              <div class="message message-error"><?php echo htmlspecialchars($withdrawal_error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if ($withdrawal_success !== ''): ?>
              <div class="message" style="background:#e7f9ee;color:#1d6b3f;"><?php echo htmlspecialchars($withdrawal_success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form class="auth-form donation-form" action="process_withdrawal.php" method="post">
              <label>
                Full name
                <input type="text" name="account_holder" placeholder="Your full name" required />
              </label>

              <label>
                Bank name
                <input type="text" name="bank_name" placeholder="e.g. First National Bank" required />
              </label>

              <label>
                Account number
                <input type="text" name="account_number" placeholder="Enter account number" required />
              </label>

              <label>
                Amount requested
                <input type="number" name="amount" placeholder="500" min="1" step="0.01" required />
              </label>

              <label>
                Notes (optional)
                <textarea name="notes" rows="4" placeholder="Add any extra details for the admin"></textarea>
              </label>

              <button class="btn btn-primary auth-submit" type="submit">Submit Withdrawal Request</button>
            </form>
          </div>

          <div class="panel">
            <h3>Recent requests</h3>
            <?php if ($withdrawals): ?>
              <div class="list">
                <?php foreach ($withdrawals as $withdrawal): ?>
                  <div class="list-item">
                    <strong><?php echo htmlspecialchars($withdrawal['account_holder'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <div><?php echo htmlspecialchars($withdrawal['bank_name'], ENT_QUOTES, 'UTF-8'); ?> • <?php echo htmlspecialchars($withdrawal['account_number'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div>Amount: <?php echo htmlspecialchars($withdrawal['amount'], ENT_QUOTES, 'UTF-8'); ?> • Status: <?php echo htmlspecialchars(ucfirst($withdrawal['status']), ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p>No withdrawal requests yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
