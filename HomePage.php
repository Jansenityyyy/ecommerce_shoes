<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SenSneaks Inc. - Premium Footwear</title>

  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">

  <style>
    /* User Dropdown Styles */
    .user-menu { position: relative; }
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
    .user-name { font-weight: 600; font-size: 0.95rem; }
    .dropdown-arrow { font-size: 0.7rem; transition: transform 0.3s ease; }
    .user-menu.active .dropdown-arrow { transform: rotate(180deg); }
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
    .dropdown-menu a:hover { background: rgba(255, 157, 0, 0.1); color: #ff9d00; }
    .dropdown-menu a i { width: 20px; text-align: center; color: #ff9d00; }
    .dropdown-divider { height: 1px; background: rgba(255, 157, 0, 0.2); margin: 8px 0; }

    /* Cart Link with Badge */
    .cart-link {
      position: relative;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .cart-badge {
      position: absolute;
      top: -10px;
      right: -12px;
      background: linear-gradient(135deg, #ff9d00, #ff6600);
      color: #111;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 3px 7px;
      border-radius: 50%;
      min-width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 10px rgba(255, 157, 0, 0.4);
      animation: pulse 2s infinite;
    }
    .cart-badge.hidden { display: none; }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Notification Toast */
    .notification-toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #1c1c1c;
      border: 1px solid rgba(255, 157, 0, 0.3);
      border-radius: 10px;
      padding: 15px 25px;
      display: flex;
      align-items: center;
      gap: 12px;
      color: #fff;
      font-weight: 500;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
      transform: translateX(120%);
      transition: transform 0.3s ease;
      z-index: 9999;
    }
    .notification-toast.show { transform: translateX(0); }
    .notification-toast.success i { color: #4caf50; font-size: 1.3rem; }
    .notification-toast.error i { color: #ff6b6b; font-size: 1.3rem; }

    /* Add to Cart Button - Product Cards */
    .product-card .add-cart-btn {
      width: 100%;
      padding: 12px 20px;
      margin-top: 15px;
      background: linear-gradient(135deg, #ff9d00, #ff7700);
      border: none;
      border-radius: 25px;
      color: #111;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 157, 0, 0.3);
    }
    .product-card .add-cart-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(255, 157, 0, 0.5);
      background: linear-gradient(135deg, #ffb033, #ff8800);
    }
    .product-card .add-cart-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 10px rgba(255, 157, 0, 0.3);
    }
    .product-card .add-cart-btn i { font-size: 1rem; }

    /* Limited Product Buttons */
    .limited-info .btn-group {
      display: flex;
      gap: 15px;
      margin-top: 20px;
      flex-wrap: wrap;
    }
    .limited-info .btn-buy,
    .limited-info .btn-cart {
      padding: 15px 35px;
      border: none;
      border-radius: 30px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s ease;
    }
    .limited-info .btn-buy {
      background: linear-gradient(135deg, #ff9d00, #ff7700);
      color: #111;
      box-shadow: 0 4px 20px rgba(255, 157, 0, 0.4);
    }
    .limited-info .btn-buy:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(255, 157, 0, 0.6);
    }
    .limited-info .btn-cart {
      background: transparent;
      color: #ff9d00;
      border: 2px solid #ff9d00;
    }
    .limited-info .btn-cart:hover {
      background: rgba(255, 157, 0, 0.1);
      transform: translateY(-3px);
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
      .user-name { display: none; }
      .user-display { padding: 8px 12px; }
      .dropdown-menu { right: -10px; }
      .limited-info .btn-group { flex-direction: column; }
      .limited-info .btn-buy,
      .limited-info .btn-cart { width: 100%; justify-content: center; }
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
      <li><a href="before.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> Products</a></li>
      <li>
        <a href="cart.php" class="cart-link">
          <i class="fas fa-shopping-cart"></i> Cart
          <span class="cart-badge hidden" id="cart-badge">0</span>
        </a>
      </li>
      
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
          
          <div class="btn-group">
            <button class="btn-buy" id="shop-now"><i class="fas fa-shopping-bag"></i> Buy Now!</button>
            <button class="btn-cart" id="limited-add-cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
          </div>
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
          <a href=""><h3>Download Our App</h3></a>
          <p>Download App for Android and ios</p>
          <div class="app-logo">
            <img src="src/img/d1.png" alt="Play Store">
            <img src="src/img/d2.png" alt="App Store">
          </div>
        </div>
        <div class="footer-col-2">
          <a href="before.html"><img src="src/img/Black and Orange Shoe Brand Logo.png" alt="Logo"></a>
          <p>SenSneaks Inc.—stepping up your style with premium comfort, trendsetting designs, and exclusive footwear for every occasion.</p>
        </div>
        <div class="footer-col-3">
          <h3>Useful Links</h3>
          <ul>
            <li><i class="fa-solid fa-ticket"></i> Coupons</li>
            <li><i class="fa-solid fa-undo"></i> Return Policy</li>
            <li><i class="fa-solid fa-comment"></i> Feedback</li>
            <li><i class="fa-solid fa-handshake"></i> Join Affiliate</li>
          </ul>
        </div>
        <div class="footer-col-4">
          <h3>Follow us</h3>
          <ul>
            <li><a href="admin/index.php"><i class="fa-brands fa-facebook"></i> Facebook</a></li>
            <li><a href="https://www.instagram.com/JansenMark04"><i class="fa-brands fa-instagram"></i> Instagram</a></li>
            <li><a href="https://www.youtube.com/JansenMark04"><i class="fa-brands fa-youtube"></i> YouTube</a></li>
            <li><a href="https://www.tiktok.com/@JansenMark04"><i class="fa-brands fa-tiktok"></i> TikTok</a></li>
          </ul>
        </div>
      </div>
      <hr>
      <p class="copyright">© 2025 SenSneaks Inc. All rights reserved.</p>
    </div>
  </div>
  
  <script src="src/js/index.js"></script>
  
  <script>
    function toggleDropdown() {
      document.querySelector('.user-menu').classList.toggle('active');
    }
    document.addEventListener('click', function(event) {
      const userMenu = document.querySelector('.user-menu');
      if (userMenu && !userMenu.contains(event.target)) {
        userMenu.classList.remove('active');
      }
    });
  </script>

</body>
</html>