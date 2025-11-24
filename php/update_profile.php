<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'update_account') {
    updateAccount($conn, $user_id);
} elseif ($action === 'update_password') {
    updatePassword($conn, $user_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

mysqli_close($conn);

// Update Account Information
function updateAccount($conn, $user_id) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Validate input
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }

    // Check if username already exists (excluding current user)
    $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $username, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        return;
    }

    // Check if email already exists (excluding current user)
    $check_email_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_email_stmt = mysqli_prepare($conn, $check_email_sql);
    mysqli_stmt_bind_param($check_email_stmt, "si", $email, $user_id);
    mysqli_stmt_execute($check_email_stmt);
    $check_email_result = mysqli_stmt_get_result($check_email_stmt);

    if (mysqli_num_rows($check_email_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        return;
    }

    // Update user account
    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Update session username
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating account']);
    }

    mysqli_stmt_close($stmt);
}

// Update Password
function updatePassword($conn, $user_id) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        return;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }

    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        return;
    }

    // Get current password hash from database
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        return;
    }

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        return;
    }

    // Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $new_password_hash, $user_id);

    if (mysqli_stmt_execute($update_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating password']);
    }

    mysqli_stmt_close($update_stmt);
}
?>