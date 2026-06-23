<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: donations.php');
    exit;
}

$amount = trim($_POST['amount'] ?? '');
$currency = trim(strtoupper($_POST['currency'] ?? ''));
$note = trim($_POST['note'] ?? '');
$user_id = (int) $_SESSION['user_id'];

if ($amount === '' || $currency === '') {
    $_SESSION['donation_error'] = 'Please enter an amount and currency.';
    header('Location: donations.php');
    exit;
}

if (!is_numeric($amount) || (float) $amount <= 0) {
    $_SESSION['donation_error'] = 'Please enter a valid donation amount.';
    header('Location: donations.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO donations (user_id, amount, currency, note, payment_method) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$user_id, number_format((float) $amount, 2, '.', ''), $currency, $note, 'manual']);

$_SESSION['donation_success'] = 'Your donation was recorded successfully.';
header('Location: donations.php');
exit;
