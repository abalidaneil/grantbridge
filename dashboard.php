<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');

$stmt = $pdo->query('SELECT id, title, category, amount FROM grant_opportunities ORDER BY id ASC');
$grants = $stmt->fetchAll();

$activityFeed = [
    ['type' => 'donation', 'name' => 'Sarah M.', 'detail' => 'donated $500 to Community Growth Fund', 'time' => '2 hours ago'],
    ['type' => 'grant', 'name' => 'James P.', 'detail' => 'accepted a $15,000 grant for Youth Skills Initiative', 'time' => '5 hours ago'],
    ['type' => 'donation', 'name' => 'Amina R.', 'detail' => 'donated $250 to Innovation Lab Grant', 'time' => 'Yesterday']
];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Dashboard</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="dashboard-shell">
      <div class="dashboard-card">
        <nav class="dashboard-nav">
          <a class="brand" href="index.html">GrantBridge</a>
          <div class="dashboard-nav-links">
            <a href="#">Overview</a>
            <a href="#">Grants</a>
            <a href="#">Donations</a>
            <a href="#">Activity</a>
          </div>
          <a class="btn btn-secondary" href="logout.php">Log Out</a>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Welcome back</span>
            <h1><?php echo $user_name; ?></h1>
            <p>Your grant activity, donations, and opportunities are ready to review.</p>
          </div>
        </div>

        <div class="summary-grid">
          <div class="summary-card">
            <h3>Grants Applied For</h3>
            <strong>3</strong>
            <p>Pending and in review</p>
          </div>
          <div class="summary-card summary-card-accent">
            <h3>Grants Accepted</h3>
            <strong>1</strong>
            <p>Successfully funded</p>
          </div>
          <div class="summary-card">
            <h3>Donations Made</h3>
            <strong>$1,250</strong>
            <p>Supported community projects</p>
          </div>
        </div>

        <div class="dashboard-grid">
          <div class="panel">
            <h3>Available Grants</h3>
            <div class="list">
              <?php foreach ($grants as $grant): ?>
                <div class="list-item">
                  <strong><?php echo htmlspecialchars($grant['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                  <div><?php echo htmlspecialchars($grant['category'], ENT_QUOTES, 'UTF-8'); ?> • <?php echo htmlspecialchars($grant['amount'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="panel">
            <h3>Community Activity</h3>
            <div class="activity-list">
              <?php foreach ($activityFeed as $item): ?>
                <div class="activity-item">
                  <div class="activity-badge"><?php echo $item['type'] === 'grant' ? 'Grant' : 'Donation'; ?></div>
                  <div>
                    <strong><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <p><?php echo htmlspecialchars($item['detail'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <span><?php echo htmlspecialchars($item['time'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
