<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["user_role"] ?? "") !== "admin") {
    $_SESSION["auth_error"] =
        "You do not have permission to access the admin area.";
    header("Location: dashboard.php");
    exit();
}

$admin_error = $_SESSION["admin_error"] ?? "";
$admin_success = $_SESSION["admin_success"] ?? "";
unset($_SESSION["admin_error"], $_SESSION["admin_success"]);

$applications_stmt = $pdo->query(
    'SELECT a.id, a.amount, a.status, a.notes, a.submitted_at, u.full_name AS applicant_name, g.title AS grant_title '
    . 'FROM applications a '
    . 'JOIN users u ON u.id = a.user_id '
    . 'JOIN grant_opportunities g ON g.id = a.grant_id '
    . 'ORDER BY a.submitted_at DESC'
);
$applications = $applications_stmt->fetchAll();


$withdrawals_stmt = $pdo->query(
    "SELECT w.id, w.amount, w.status, w.created_at, w.account_holder, w.bank_name, w.account_number, u.full_name AS applicant_name " .
        "FROM withdrawals w " .
        "JOIN users u ON u.id = w.user_id " .
        "ORDER BY w.created_at DESC",
);
$withdrawals = $withdrawals_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GrantBridge | Admin</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="dashboard-shell">
      <div class="dashboard-card">
        <nav class="dashboard-nav">
          <a class="brand" href="index.html">GrantBridge</a>
          <div class="dashboard-nav-links">
            <a href="admin.php">Admin</a>
            <a href="logout.php">Log Out</a>
          </div>
        </nav>

        <div class="dashboard-top">
          <div>
            <span class="eyebrow">Admin panel</span>
            <h1>Manage grant applications and withdrawals</h1>
            <p>Review incoming requests, update their status, and keep the processing flow organized.</p>
          </div>
          <a class="btn btn-secondary" href="create_admin.php">Create Admin Account</a>
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

        <div class="dashboard-grid">
          <div class="panel">
            <h3>Grant applications</h3>
            <?php if ($applications): ?>
              <div class="list">
                <?php foreach ($applications as $application): ?>
                  <div class="list-item">
                    <strong><?php echo htmlspecialchars(
                        $application["applicant_name"],
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?></strong>
                    <div><?php echo htmlspecialchars(
                        $application["grant_title"],
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?> • Amount: <?php echo htmlspecialchars(
     $application["amount"],
     ENT_QUOTES,
     "UTF-8",
 ); ?></div>
                    <div>Status: <?php echo htmlspecialchars(
                        ucfirst($application["status"]),
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?></div>
                    <?php if (!empty($application["notes"])): ?>
                      <div>Notes: <?php echo htmlspecialchars(
                          $application["notes"],
                          ENT_QUOTES,
                          "UTF-8",
                      ); ?></div>
                    <?php endif; ?>
                    <form class="admin-actions" method="post" action="process_admin_action.php">
                      <input type="hidden" name="type" value="application" />
                      <input type="hidden" name="id" value="<?php echo (int) $application[
                          "id"
                      ]; ?>" />
                      <select name="status">
                        <option value="submitted" <?php echo $application[
                            "status"
                        ] === "submitted"
                            ? "selected"
                            : ""; ?>>Submitted</option>
                        <option value="approved" <?php echo $application[
                            "status"
                        ] === "approved"
                            ? "selected"
                            : ""; ?>>Approved</option>
                        <option value="rejected" <?php echo $application[
                            "status"
                        ] === "rejected"
                            ? "selected"
                            : ""; ?>>Rejected</option>
                      </select>
                      <button class="btn btn-secondary" type="submit">Update</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p>No grant applications have been submitted yet.</p>
            <?php endif; ?>
          </div>

          <div class="panel">
            <h3>Withdrawal requests</h3>
            <?php if ($withdrawals): ?>
              <div class="list">
                <?php foreach ($withdrawals as $withdrawal): ?>
                  <div class="list-item">
                    <strong><?php echo htmlspecialchars(
                        $withdrawal["applicant_name"],
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?></strong>
                    <div>Bank: <?php echo htmlspecialchars(
                        $withdrawal["bank_name"],
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?> • Account: <?php echo htmlspecialchars(
     $withdrawal["account_number"],
     ENT_QUOTES,
     "UTF-8",
 ); ?></div>
                    <div>Amount: <?php echo htmlspecialchars(
                        $withdrawal["amount"],
                        ENT_QUOTES,
                        "UTF-8",
                    ); ?> • Status: <?php echo htmlspecialchars(
     ucfirst($withdrawal["status"]),
     ENT_QUOTES,
     "UTF-8",
 ); ?></div>
                    <form class="admin-actions" method="post" action="process_admin_action.php">
                      <input type="hidden" name="type" value="withdrawal" />
                      <input type="hidden" name="id" value="<?php echo (int) $withdrawal[
                          "id"
                      ]; ?>" />
                      <select name="status">
                        <option value="pending" <?php echo $withdrawal[
                            "status"
                        ] === "pending"
                            ? "selected"
                            : ""; ?>>Pending</option>
                        <option value="approved" <?php echo $withdrawal[
                            "status"
                        ] === "approved"
                            ? "selected"
                            : ""; ?>>Approved</option>
                        <option value="rejected" <?php echo $withdrawal[
                            "status"
                        ] === "rejected"
                            ? "selected"
                            : ""; ?>>Rejected</option>
                      </select>
                      <button class="btn btn-secondary" type="submit">Update</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p>No withdrawal requests are waiting for review.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
