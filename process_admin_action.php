<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    $_SESSION['admin_error'] = 'You do not have permission to update requests.';
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$type = $_POST['type'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = trim($_POST['status'] ?? '');

if ($type === '' || $id === false || $id <= 0 || !in_array($status, ['submitted','approved','rejected','pending'], true)) {
    $_SESSION['admin_error'] = 'Invalid request details.';
    header('Location: admin.php');
    exit;
}

if ($type === 'application') {
    $stmt = $pdo->prepare('UPDATE applications SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
} elseif ($type === 'withdrawal') {
    $stmt = $pdo->prepare('UPDATE withdrawals SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
} else {
    $_SESSION['admin_error'] = 'Unknown request type.';
    header('Location: admin.php');
    exit;
}

$_SESSION['admin_success'] = 'The request status was updated successfully.';
header('Location: admin.php');
exit;
