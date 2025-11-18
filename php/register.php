<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php?error=invalid_request");
    exit();
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

if (empty($username) || empty($email) || empty($password)) {
    header("Location: ../login.php?error=empty_fields");
    exit();
}

// Check if user exists
$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: ../login.php?error=user_exists");
    exit();
}

mysqli_stmt_close($stmt);

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$insertSql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);

if (mysqli_stmt_execute($insertStmt)) {

    // Auto-login after registration
    $user_id = mysqli_insert_id($conn);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;

    header("Location: ../HomePage.php?register=success");
    exit();
} else {
    header("Location: ../login.php?error=registration_failed");
    exit();
}

mysqli_stmt_close($insertStmt);
mysqli_close($conn);
?>
