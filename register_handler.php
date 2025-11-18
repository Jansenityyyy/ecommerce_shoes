<?php
session_start();
include 'php/connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if(empty($username) || empty($email) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }
    
    // Check if username already exists
    $checkSql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ss", $username, $email);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if(mysqli_num_rows($checkResult) > 0) {
        // Username or email already exists
        mysqli_stmt_close($checkStmt);
        header("Location: login.php?error=user_exists");
        exit();
    }
    mysqli_stmt_close($checkStmt);
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);
    
    if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Registration successful, redirect to login
        header("Location: login.php?register=success");
        exit();
    } else {
        mysqli_stmt_close($stmt);
        // Registration failed
        header("Location: login.php?error=registration_failed");
        exit();
    }
} else {
    // Not a POST request
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>