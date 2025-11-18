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
    overflow-x:hidden;
    position:relative;
}
body::before {
    content:''; position:absolute; top:0; left:0; right:0; bottom:0;
    background: radial-gradient(circle at 30% 50%, rgba(255,157,0,0.1) 0%,transparent 50%),
                radial-gradient(circle at 70% 50%, rgba(255,157,0,0.05) 0%,transparent 50%);
    pointer-events:none;
}

/* Alerts */
.alert {position:fixed; top:100px; left:50%; transform:translateX(-50%);
    padding:15px 30px; border-radius:10px; font-weight:600; z-index:2000; animation:slideDown 0.5s ease; max-width:400px; text-align:center;}
.alert-error {background:rgba(231,76,60,0.15); border:1px solid #e74c3c; color:#e74c3c;}
.alert-success {background:rgba(0,255,0,0.15); border:1px solid #00ff00; color:#00ff00;}
@keyframes slideDown {from{top:-50px;opacity:0;} to{top:100px;opacity:1;}}

/* Navbar */
nav {position:fixed; top:0;width:100%;display:flex;justify-content:space-between;align-items:center;padding:20px 50px;background:rgba(28,28,28,0.95);backdrop-filter:blur(10px);box-shadow:0 2px 20px rgba(0,0,0,0.5);z-index:1000;}
nav .logo {font-size:2rem;font-weight:bold;color:#ff9d00;font-family:'Amsterdam One',sans-serif;letter-spacing:2px;}
nav .nav-links {list-style:none;display:flex;gap:35px;}
nav .nav-links li a {color:#ff9d00;text-decoration:none;font-weight:500;font-size:1rem;position:relative;transition:all 0.3s ease;}
nav .nav-links li a:hover {color:#fff;}
nav .nav-links li a::after {content:'';position:absolute;bottom:-5px;left:0;width:0;height:2px;background:#ff9d00;transition:width 0.3s ease;}
nav .nav-links li a:hover::after {width:100%;}

/* Auth container */
.auth-container {position:relative;width:850px;height:550px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,157,0,0.1);border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,0.3);overflow:hidden;z-index:1; animation:fadeIn 0.8s ease;}
.forms-container {position:absolute;width:100%;height:100%;transition:all 0.6s ease-in-out; z-index:10;}
.signin-signup {position:absolute;top:50%;left:75%;transform:translate(-50%,-50%);width:50%;display:grid;z-index:11;}
form {display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0 5rem;overflow:hidden;grid-column:1/2;grid-row:1/2;transition:0.2s 0.7s ease-in-out;}
form.sign-in-form {z-index:2;opacity:1;pointer-events:auto;}
form.sign-up-form {z-index:1;opacity:0;pointer-events:none;}
.auth-container.sign-up-mode form.sign-in-form {z-index:1;opacity:0;pointer-events:none;}
.auth-container.sign-up-mode form.sign-up-form {z-index:2;opacity:1;pointer-events:auto;}

.title {font-size:2.2rem;color:#fff;margin-bottom:10px;font-weight:700;}
.input-field {max-width:380px;width:100%;height:55px;background:rgba(255,255,255,0.08);margin:10px 0;border-radius:10px;display:grid;grid-template-columns:15% 85%;padding:0 0.4rem;border:1px solid rgba(255,157,0,0.2);transition:all 0.3s ease;}
.input-field:focus-within {border-color:#ff9d00;background:rgba(255,255,255,0.12);box-shadow:0 0 15px rgba(255,157,0,0.2);}
.input-field i {text-align:center;line-height:55px;color:#ff9d00;font-size:1.1rem;}
.input-field input {background:none;outline:none;border:none;line-height:1;font-weight:500;font-size:1rem;color:#fff;font-family:'Poppins',sans-serif;}
.input-field input::placeholder {color:#aaa;}
.btn {width:150px;height:49px;border:none;outline:none;border-radius:50px;cursor:pointer;background:linear-gradient(135deg,#ff9d00 0%,#ff7700 100%);color:#111;text-transform:uppercase;font-weight:700;margin:10px 0;transition:all 0.4s ease;box-shadow:0 10px 30px rgba(255,157,0,0.3);position:relative;overflow:hidden;}
.btn:hover {transform:translateY(-3px);box-shadow:0 15px 40px rgba(255,157,0,0.5);}
.btn::before {content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(135deg,#fff 0%,#ff9d00 100%);transition:left 0.4s ease;z-index:-1;}
.btn:hover::before {left:0;}

.panels-container {position:absolute;width:100%;height:100%;top:0;left:0;display:grid;grid-template-columns:repeat(2,1fr);}
.panel {display:flex;flex-direction:column;align-items:flex-end;justify-content:space-around;text-align:center;z-index:7;padding:3rem 17% 2rem 12%;}
.left-panel {pointer-events:all;padding:3rem 12% 2rem 17%;}
.panel .content {color:#fff;transition:0.9s 0.6s ease-in-out;}
.panel h3 {font-weight:700;line-height:1;font-size:2rem;margin-bottom:10px;}
.panel p {font-size:0.95rem;padding:0.7rem 0;color:#ccc;line-height:1.6;}
.btn.transparent {margin:0;background:none;border:2px solid #ff9d00;width:180px;height:49px;font-weight:700;font-size:0.9rem;color:#ff9d00;box-shadow:none;}
.btn.transparent:hover {background:rgba(255,157,0,0.1);color:#fff;border-color:#fff;}
.image {width:100%;transition:1.1s 0.4s ease-in-out;}
.right-panel .content,.right-panel .image {transform:translateX(800px);}
.auth-container::before {content:'';position:absolute;width:2000px;height:2000px;border-radius:50%;background:linear-gradient(135deg,rgba(255,157,0,0.15),rgba(255,119,0,0.1));top:50%;right:48%;transform:translateY(-50%);z-index:6;transition:1.8s ease-in-out;}

@keyframes fadeIn {from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);}}
</style>
</head>
<body>

<!-- Alerts -->
<?php if($error): ?>
    <div class="alert alert-error" id="alert">
        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
    </div>
<?php endif; ?>
<?php if($success): ?>
    <div class="alert alert-success" id="alert">
        <i class="fas fa-check-circle"></i> <?= $success ?>
    </div>
<?php endif; ?>

<!-- Navbar -->
<nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="#"><i class="fas fa-shopping-bag"></i> Products</a></li>
        <li><a href="#"><i class="fas fa-user"></i> Login</a></li>
    </ul>
</nav>

<div class="auth-container">
    <div class="forms-container">
        <div class="signin-signup">
            <!-- Sign In Form -->
            <form action="php/login.php" method="POST" class="sign-in-form">
                <h2 class="title"><i class="fas fa-sign-in-alt"></i> Sign In</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required />
                </div>
                <button type="submit" class="btn">Login</button>
            </form>

            <!-- Sign Up Form -->
            <form action="php/register.php" method="POST" class="sign-up-form">
                <h2 class="title"><i class="fas fa-user-plus"></i> Sign Up</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required />
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required />
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
        </div>
    </div>

    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>New here?</h3>
                <p>Join SenSneaks Inc. today and discover exclusive deals on premium footwear!</p>
                <button class="btn transparent" id="sign-up-btn">Sign Up</button>
            </div>
            <img src="src/img/logo.png" class="image" alt="Sneaker" />
        </div>
        <div class="panel right-panel">
            <div class="content">
                <h3>Already a member?</h3>
                <p>Welcome back! Sign in to access your account and continue shopping.</p>
                <button class="btn transparent" id="sign-in-btn">Sign In</button>
            </div>
            <img src="src/img/logo.png" class="image" alt="Sneaker" />
        </div>
    </div>
</div>

<script>
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".auth-container");

sign_up_btn.addEventListener("click", () => {
    container.classList.add("sign-up-mode");
});
sign_in_btn.addEventListener("click", () => {
    container.classList.remove("sign-up-mode");
});

// Auto-hide alert after 5 seconds
const alertBox = document.getElementById('alert');
if(alertBox){
    setTimeout(()=>{
        alertBox.style.animation = 'slideDown 0.5s ease reverse';
        setTimeout(()=> alertBox.remove(),500);
    },5000);
}
</script>

</body>
</html>
