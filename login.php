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
    position: relative;
    overflow: hidden;
}

/* Animated background particles */
body::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,157,0,0.1) 0%, transparent 70%);
    border-radius: 50%;
    top: -100px;
    left: -100px;
    animation: float 20s infinite ease-in-out;
}

body::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,119,0,0.08) 0%, transparent 70%);
    border-radius: 50%;
    bottom: -80px;
    right: -80px;
    animation: float 15s infinite ease-in-out reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(50px, 50px) rotate(180deg); }
}

/* Alerts */
.alert {
    position:fixed; 
    top:30px; 
    left:50%; 
    transform:translateX(-50%);
    padding:18px 35px; 
    border-radius:15px; 
    font-weight:500; 
    z-index:2000; 
    max-width:450px; 
    text-align:center;
    backdrop-filter: blur(10px);
    animation: slideDown 0.5s ease-out;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

@keyframes slideDown {
    from { top: -100px; opacity: 0; }
    to { top: 30px; opacity: 1; }
}

.alert-error {
    background:rgba(231,76,60,0.95); 
    border:2px solid rgba(255,255,255,0.2);
    color:#fff;
}

.alert-success {
    background:rgba(0,255,0,0.95); 
    border:2px solid rgba(255,255,255,0.2);
    color:#fff;
}

.alert i {
    margin-right: 10px;
    font-size: 1.2rem;
}

/* Brand Logo */
.brand-header {
    position: absolute;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    font-family: 'Amsterdam One', cursive;
    font-size: 2rem;
    color: #ff9d00;
    z-index: 10;
    text-shadow: 0 0 20px rgba(255,157,0,0.3);
}

/* Auth container */
.auth-container {
    display:flex;
    width:900px;
    min-height:580px;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 30px 80px rgba(0,0,0,0.5);
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,157,0,0.2);
    position: relative;
    z-index: 1;
    animation: fadeIn 0.6s ease-out;
    backdrop-filter: blur(10px);
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

/* Forms */
.form-panel {
    width:50%;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    padding:3rem 2.5rem;
    position: relative;
}

.form-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9d00 0%, #ff7700 100%);
}

.form-panel h2 {
    font-size:2.2rem;
    margin-bottom:15px;
    font-weight:700;
    color: #ff9d00;
    text-shadow: 0 0 20px rgba(255,157,0,0.3);
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-panel h2 i {
    font-size: 2rem;
}

.form-panel p {
    color: #aaa;
    margin-bottom: 30px;
    font-size: 0.95rem;
}

form {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.input-field {
    width:100%;
    height:55px;
    margin-bottom:20px;
    border-radius:12px;
    display:flex;
    align-items:center;
    padding:0 20px;
    background:rgba(255,255,255,0.05);
    border:2px solid rgba(255,157,0,0.2);
    transition: all 0.3s ease;
}

.input-field:focus-within {
    border-color: #ff9d00;
    background: rgba(255,255,255,0.08);
    box-shadow: 0 5px 25px rgba(255,157,0,0.2);
    transform: translateY(-2px);
}

.input-field i {
    color:#ff9d00;
    margin-right:15px;
    font-size:1.2rem;
}

.input-field input {
    flex:1;
    background:none;
    border:none;
    outline:none;
    color:#fff;
    font-size:1rem;
    font-weight: 400;
}

.input-field input::placeholder {
    color:#888;
}

.btn {
    width:100%;
    height:55px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
    color:#111;
    font-weight:700;
    font-size: 1.05rem;
    cursor:pointer;
    transition:all 0.3s ease;
    box-shadow: 0 8px 25px rgba(255,157,0,0.3);
    margin-top: 10px;
}

.btn:hover {
    transform:translateY(-3px);
    box-shadow:0 12px 35px rgba(255,157,0,0.5);
}

.btn:active {
    transform: translateY(-1px);
}

/* Panel styling */
.panel-left {
    border-right:1px solid rgba(255,157,0,0.15);
}

.panel-right {
    background: rgba(255,157,0,0.02);
}

/* Responsive */
@media (max-width: 768px) {
    .auth-container {
        width: 95%;
        flex-direction: column;
        height: auto;
    }
    
    .form-panel {
        width: 100%;
        border-right: none !important;
    }
    
    .panel-left {
        border-bottom: 1px solid rgba(255,157,0,0.2);
    }
    
    .brand-header {
        position: static;
        transform: none;
        margin-bottom: 30px;
        font-size: 1.6rem;
    }
}
</style>
</head>
<body>

<div class="brand-header">SenSneaks Inc.</div>

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
    <div class="form-panel panel-left">
        <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
        <p>Sign in to continue to SenSneaks</p>
        <form action="php/login.php" method="POST">
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>

    <!-- Sign Up Form -->
    <div class="form-panel panel-right">
        <h2><i class="fas fa-user-plus"></i> Create Account</h2>
        <p>Join SenSneaks today</p>
        <form action="php/register.php" method="POST">
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
</div>

</body>
</html>