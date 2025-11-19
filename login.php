<?php
session_start();

// If user is already logged in, redirect to home
if(isset($_SESSION['user_id'])) {
    header("Location: HomePage.php");
    exit();
}

// Handle error messages
$error = '';
if(isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_credentials':
            $error = 'Invalid username or password!';
            break;
        case 'user_not_found':
            $error = 'User not found!';
            break;
        case 'user_exists':
            $error = 'Username or email already exists!';
            break;
        case 'registration_failed':
            $error = 'Registration failed. Please try again.';
            break;
    }
}

// Handle success messages
$success = '';
if(isset($_GET['register']) && $_GET['register'] == 'success') {
    $success = 'Registration successful! Please login.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - SenSneaks Inc.</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg,#2c2c2c,#1a1a1a);
    color:#fff;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Alerts */
.alert {position:fixed; top:100px; left:50%; transform:translateX(-50%);
    padding:15px 30px; border-radius:10px; font-weight:600; z-index:2000; max-width:400px; text-align:center;}
.alert-error {background:rgba(231,76,60,0.15); border:1px solid #e74c3c; color:#e74c3c;}
.alert-success {background:rgba(0,255,0,0.15); border:1px solid #00ff00; color:#00ff00;}

/* Auth container */
.auth-container {
    display:flex;
    width:850px;
    height:550px;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,157,0,0.1);
}

/* Forms */
form {
    width:50%;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:2rem;
}
form h2 {
    font-size:2rem;
    margin-bottom:20px;
    font-weight:700;
    color:#fff;
}
.input-field {
    width:100%;
    height:50px;
    margin-bottom:15px;
    border-radius:10px;
    display:flex;
    align-items:center;
    padding:0 10px;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,157,0,0.2);
}
.input-field i {
    color:#ff9d00;
    margin-right:10px;
    font-size:1.1rem;
}
.input-field input {
    flex:1;
    background:none;
    border:none;
    outline:none;
    color:#fff;
    font-size:1rem;
}
.input-field input::placeholder {
    color:#aaa;
}
.btn {
    width:150px;
    height:45px;
    border:none;
    border-radius:50px;
    background:linear-gradient(135deg,#ff9d00,#ff7700);
    color:#111;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
}
.btn:hover {
    transform:translateY(-3px);
    box-shadow:0 10px 30px rgba(255,157,0,0.3);
}

/* Panel styling */
.panel-left {border-right:1px solid rgba(255,157,0,0.2);}
.panel-right {border-left:1px solid rgba(255,157,0,0.2);}
</style>
</head>
<body>

<!-- Alerts -->
<?php if($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
    </div>
<?php endif; ?>
<?php if($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= $success ?>
    </div>
<?php endif; ?>

<div class="auth-container">
    <!-- Sign In Form -->
    <form action="php/login.php" method="POST" class="panel-left">
        <h2><i class="fas fa-sign-in-alt"></i> Sign In</h2>
        <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <!-- Sign Up Form -->
    <form action="php/register.php" method="POST" class="panel-right">
        <h2><i class="fas fa-user-plus"></i> Sign Up</h2>
        <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-field">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn">Sign Up</button>
    </form>
</div>

</body>
</html>
