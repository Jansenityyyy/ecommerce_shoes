<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php?error=invalid_request");
    exit();
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

// Check user
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    if (password_verify($password, $user['password'])) {

        // Set sessions
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        header("Location: ../HomePage.php?login=success");
        exit();
    } else {
        header("Location: ../login.php?error=invalid_password");
        exit();
    }

} else {
    header("Location: ../login.php?error=user_not_found");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
