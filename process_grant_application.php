<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply_grant.php');
    exit;
}

$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$grant_id = filter_input(INPUT_POST, 'grant_id', FILTER_VALIDATE_INT);
$notes = trim($_POST['notes'] ?? '');

if ($amount === false || $amount <= 0) {
    $_SESSION['application_error'] = 'Please enter a valid amount.';
    header('Location: apply_grant.php');
    exit;
}

if ($grant_id === false || $grant_id <= 0) {
    $_SESSION['application_error'] = 'Please select a grant opportunity.';
    header('Location: apply_grant.php');
    exit;
}

$document_path = null;

if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['application_error'] = 'The document upload failed. Please try again.';
        header('Location: apply_grant.php');
        exit;
    }

    if ($_FILES['document']['size'] > 2 * 1024 * 1024) {
        $_SESSION['application_error'] = 'The document must be 2MB or smaller.';
        header('Location: apply_grant.php');
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['document']['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mime, $allowed_types, true)) {
        $_SESSION['application_error'] = 'Only JPG, PNG, or WEBP images are allowed.';
        header('Location: apply_grant.php');
        exit;
    }

    $upload_dir = __DIR__ . '/uploads/grant-docs';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
        $_SESSION['application_error'] = 'The upload folder could not be created.';
        header('Location: apply_grant.php');
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
    $filename = 'document_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $target_path = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($_FILES['document']['tmp_name'], $target_path)) {
        $_SESSION['application_error'] = 'The document could not be saved.';
        header('Location: apply_grant.php');
        exit;
    }

    $document_path = 'uploads/grant-docs/' . $filename;
}

try {
    $pdo->exec("ALTER TABLE applications ADD COLUMN amount DECIMAL(10,2) NOT NULL DEFAULT 0");
} catch (PDOException $e) {
    // Column already exists.
}

try {
    $pdo->exec("ALTER TABLE applications ADD COLUMN document_path VARCHAR(255) DEFAULT NULL");
} catch (PDOException $e) {
    // Column already exists.
}

try {
    $pdo->exec("ALTER TABLE applications ADD COLUMN notes TEXT DEFAULT NULL");
} catch (PDOException $e) {
    // Column already exists.
}

$stmt = $pdo->prepare('INSERT INTO applications (user_id, grant_id, amount, document_path, notes, status) VALUES (?, ?, ?, ?, ?, "submitted")');
$stmt->execute([$_SESSION['user_id'], $grant_id, $amount, $document_path, $notes]);

$_SESSION['application_success'] = 'Your grant application has been submitted successfully.';
header('Location: apply_grant.php');
exit;
