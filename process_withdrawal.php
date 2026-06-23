<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: withdrawal.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$account_holder = trim($_POST['account_holder'] ?? '');
$bank_name = trim($_POST['bank_name'] ?? '');
$account_number = trim($_POST['account_number'] ?? '');
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$notes = trim($_POST['notes'] ?? '');

if ($account_holder === '' || $bank_name === '' || $account_number === '') {
    $_SESSION['withdrawal_error'] = 'Please complete all required payout details.';
    header('Location: withdrawal.php');
    exit;
}

if ($amount === false || $amount <= 0) {
    $_SESSION['withdrawal_error'] = 'Please enter a valid amount.';
    header('Location: withdrawal.php');
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS withdrawals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        account_holder VARCHAR(150) NOT NULL,
        bank_name VARCHAR(150) NOT NULL,
        account_number VARCHAR(100) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        notes TEXT DEFAULT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    // Continue if table already exists.
}

$stmt = $pdo->prepare('INSERT INTO withdrawals (user_id, account_holder, bank_name, account_number, amount, notes, status) VALUES (?, ?, ?, ?, ?, ?, "pending")');
$stmt->execute([$user_id, $account_holder, $bank_name, $account_number, $amount, $notes]);

$_SESSION['withdrawal_success'] = 'Your withdrawal request has been submitted and is pending admin review.';
header('Location: withdrawal.php');
exit;
