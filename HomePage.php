<?php
session_start();
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SenSneaks Inc. - Premium Footwear</title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="style.css">

  <!-- Google Fonts actually used -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/favicon.ico">

  <style>
    /* User Dropdown Styles */
    .user-menu {
      position: relative;
    }

    .user-display {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #ff9d00;
      cursor: pointer;
      padding: 8px 15px;
      border-radius: 25px;
      transition: all 0.3s ease;
      background: rgba(255, 157, 0, 0.1);
      border: 1px solid rgba(255, 157, 0, 0.2);
    }

    .user-display:hover {
      background: rgba(255, 157, 0, 0.2);
      border-color: #ff9d00;
      color: #fff;
    }

    .user-avatar {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: #111;
      font-size: 1rem;
    }

    .user-name {
      font-weight: 600;
      font-size: 0.95rem;
    }

    .dropdown-arrow {
      font-size: 0.7rem;
      transition: transform 0.3s ease;
    }

    .user-menu.active .dropdown-arrow {
      transform: rotate(180deg);
    }

    .dropdown-menu {
      position: absolute;
      top: 120%;
      right: 0;
      background: rgba(28, 28, 28, 0.98);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 157, 0, 0.2);
      border-radius: 15px;
      padding: 10px 0;
      min-width: 200px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .user-menu.active .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      color: #fff;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }

    .dropdown-menu a:hover {
      background: rgba(255, 157, 0, 0.1);
      color: #ff9d00;
    }

    .dropdown-menu a i {
      width: 20px;
      text-align: center;
      color: #ff9d00;
    }

    .dropdown-divider {
      height: 1px;
      background: rgba(255, 157, 0, 0.2);
      margin: 8px 0;
    }

    /* Login Link Style */
    nav .nav-links li a.login-link {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 20px;
      border-radius: 25px;
      background: rgba(255, 157, 0, 0.1);
      border: 1px solid rgba(255, 157, 0, 0.2);
      transition: all 0.3s ease;
    }

    nav .nav-links li a.login-link:hover {
      background: rgba(255, 157, 0, 0.2);
      border-color: #ff9d00;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .user-name {
        display: none;
      }

      .user-display {
        padding: 8px 12px;
      }

      .dropdown-menu {
        right: -10px;
      }
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
      <li><a href="before.html"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="#other-products"><i class="fas fa-shopping-bag"></i> Products</a></li>
      <li><a href="#"><i class="fas fa-shopping-cart"></i> Cart</a></li>
      
<?php if($isLoggedIn): ?>
    <li class="user-menu">
        <div class="user-display" onclick="toggleDropdown()">
            <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
            <span class="user-name"><?= htmlspecialchars($username) ?></span>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
        </div>
        <div class="dropdown-menu">
            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
            <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <div class="dropdown-divider"></div>
            <a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </li>
<?php else: ?>
    <li><a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
<?php endif; ?>

    </ul>
  </nav>

  <!-- Hero / Limited Product -->
  <section id="limited-product">
    <div class="container">
      <div class="limited-card">
        <img id="limited-img" src="" alt="Limited Shoe">
        <div class="limited-info">
          <h2 id="limited-name">Loading…</h2>
          <p id="limited-desc"></p>
          <p id="limited-price"></p>
          
          <!-- Countdown Timer -->
          <div class="countdown-container" id="countdown-container" style="display: none;">
            <div class="countdown-label"><i class="fas fa-clock"></i> Offer Ends In:</div>
            <div class="countdown-timer">
              <div class="countdown-box">
                <div class="countdown-number" id="days">00</div>
                <div class="countdown-text">Days</div>
              </div>
              <div class="countdown-box">
                <div class="countdown-number" id="hours">00</div>
                <div class="countdown-text">Hours</div>
              </div>
              <div class="countdown-box">
                <div class="countdown-number" id="minutes">00</div>
                <div class="countdown-text">Minutes</div>
              </div>
              <div class="countdown-box">
                <div class="countdown-number" id="seconds">00</div>
                <div class="countdown-text">Seconds</div>
              </div>
            </div>
          </div>
          
          <button id="shop-now"><i class="fas fa-shopping-bag"></i> Buy Now!</button>
          <button><i class="fas fa-shopping-cart"Add cart></i>Add to Cart</button>
        </div>
      </div>
    </div>
  </section>

  <!-- Other Products -->
  <section id="other-products">
    <h2><i class="fas fa-fire"></i> BEST SELLING SHOES!</h2>
    <div class="product-grid" id="productList"></div>
  </section>

  <!-------------footer--------------->
  <div class="footer" id="contact">
    <div class="container">
      <div class="row">
        <div class="footer-col-1">
          <a href="">
            <h3>Download Our App</h3>
          </a>
          <p>Download App for Android and ios</p>
          <div class="app-logo">
            <img src="src/img/d1.png" alt="Play Store">
            <img src="src/img/d2.png" alt="App Store">
          </div>
        </div>
        <div class="footer-col-2">
          <a href="before.html"><img src="src/img/Black and Orange Shoe Brand Logo.png" alt="Logo"></a>
          <p>SenSneaks Inc.—stepping up your style with premium comfort, trendsetting designs, and exclusive footwear for
            every occasion.</p>
        </div>
        <div class="footer-col-3">
          <h3>Useful Links</h3>
          <ul>
            <li><i class="fa-solid fa-ticket fa-1g"></i> Coupons</li>
            <li><i class="fa-solid fa-undo fa-1g"></i> Return Policy</li>
            <li><i class="fa-solid fa-comment fa-1g"></i> Feedback</li>
            <li><i class="fa-solid fa-handshake fa-1g"></i> Join Affiliate</li>
          </ul>
        </div>
        <div class="footer-col-4">
          <h3>Follow us</h3>
          <ul>
            <li><a href="admin/index.php">
                <i class="fa-brands fa-facebook fa-1g"></i> Facebook</a></li>
            <li><a href="https://www.instagram.com/JansenMark04">
                <i class="fa-brands fa-instagram fa-1g"></i> Instagram</a></li>
            <li><a href="https://www.youtube.com/JansenMark04">
                <i class="fa-brands fa-youtube fa-1g"></i> YouTube</a></li>
            <li><a href="https://www.tiktok.com/@JansenMark04">
                <i class="fa-brands fa-tiktok fa-1g"></i> TikTok</a></li>
          </ul>
        </div>
      </div>
      <hr>
      <p class="copyright">© 2025 SenSneaks Inc. All rights reserved.</p>
    </div>
  </div>
  
  <!-- JS -->
  <script src="src/js/index.js"></script>
  
  <script>
    // Toggle Dropdown Menu
    function toggleDropdown() {
      const userMenu = document.querySelector('.user-menu');
      userMenu.classList.toggle('active');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const userMenu = document.querySelector('.user-menu');
      if (userMenu && !userMenu.contains(event.target)) {
        userMenu.classList.remove('active');
      }
    });
  </script>

</body>

</html>