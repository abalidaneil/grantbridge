<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($full_name === '' || $email === '' || $password === '' || $confirm_password === '') {
    $_SESSION['auth_error'] = 'Please fill in all fields.';
    header('Location: signup.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['auth_error'] = 'Please enter a valid email address.';
    header('Location: signup.php');
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['auth_error'] = 'Passwords do not match.';
    header('Location: signup.php');
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$check->execute([$email]);

if ($check->fetch()) {
    $_SESSION['auth_error'] = 'An account with that email already exists.';
    header('Location: signup.php');
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)');
$insert->execute([$full_name, $email, $password_hash]);

$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['user_name'] = $full_name;

header('Location: dashboard.php');
exit;
