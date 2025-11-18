<?php
session_start();
include 'connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Query to find user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($user = mysqli_fetch_assoc($result)) {
        // Verify password (assuming you're using password_hash)
        if(password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to home page
            header("Location: ../index.php?login=success");
            exit();
        } else {
            // Wrong password
            header("Location: ../login.php?error=invalid_credentials");
            exit();
        }
    } else {
        // User not found
        header("Location: ../login.php?error=user_not_found");
        exit();
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>