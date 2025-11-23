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

    /* ============ LIMITED PRODUCT SECTION REDESIGN ============ */
    #limited-product {
      padding: 120px 50px 60px;
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      position: relative;
      overflow: hidden;
    }

    #limited-product::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(255, 157, 0, 0.1) 0%, transparent 70%);
      pointer-events: none;
    }

    .limited-wrapper {
      max-width: 1300px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 350px;
      gap: 40px;
      align-items: center;
    }

    /* Product Card */
    .limited-card {
      display: flex;
      background: rgba(255, 255, 255, 0.03);
      border-radius: 30px;
      overflow: hidden;
      border: 1px solid rgba(255, 157, 0, 0.15);
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
      position: relative;
    }

    .limited-badge {
      position: absolute;
      top: 20px;
      left: 20px;
      background: linear-gradient(135deg, #ff3d00, #ff9d00);
      color: #fff;
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.85rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 4px 15px rgba(255, 61, 0, 0.4);
      z-index: 10;
      animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {
      0%, 100% { box-shadow: 0 4px 15px rgba(255, 61, 0, 0.4); }
      50% { box-shadow: 0 4px 25px rgba(255, 61, 0, 0.7); }
    }

    .limited-card img {
      width: 45%;
      object-fit: cover;
      background: linear-gradient(145deg, #2a2a2a, #1a1a1a);
      padding: 30px;
    }

    .limited-info {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .limited-info h2 {
      font-size: 2rem;
      color: #fff;
      margin-bottom: 15px;
      line-height: 1.2;
    }

    .limited-info .product-desc {
      color: #aaa;
      font-size: 1rem;
      line-height: 1.7;
      margin-bottom: 20px;
    }

    .limited-info .product-price {
      font-size: 2.5rem;
      font-weight: 700;
      color: #ff9d00;
      margin-bottom: 25px;
    }

    .limited-info .product-price .old-price {
      font-size: 1.2rem;
      color: #666;
      text-decoration: line-through;
      margin-left: 10px;
      font-weight: 400;
    }

    .btn-group {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn-buy, .btn-cart {
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

    .btn-buy {
      background: linear-gradient(135deg, #ff9d00, #ff7700);
      color: #111;
      box-shadow: 0 4px 20px rgba(255, 157, 0, 0.4);
    }

    .btn-buy:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(255, 157, 0, 0.6);
    }

    .btn-cart {
      background: transparent;
      color: #ff9d00;
      border: 2px solid #ff9d00;
    }

    .btn-cart:hover {
      background: rgba(255, 157, 0, 0.1);
      transform: translateY(-3px);
    }

    /* ============ COUNTDOWN TIMER REDESIGN ============ */
    .countdown-section {
      background: linear-gradient(145deg, rgba(255, 157, 0, 0.1), rgba(255, 100, 0, 0.05));
      border: 1px solid rgba(255, 157, 0, 0.2);
      border-radius: 25px;
      padding: 35px 25px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .countdown-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #ff9d00, #ff5500, #ff9d00);
      background-size: 200% 100%;
      animation: shimmer 2s linear infinite;
    }

    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    .countdown-header {
      margin-bottom: 25px;
    }

    .countdown-header i {
      font-size: 2.5rem;
      color: #ff9d00;
      margin-bottom: 10px;
      display: block;
      animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }

    .countdown-header h3 {
      color: #fff;
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .countdown-header p {
      color: #888;
      font-size: 0.9rem;
    }

    .countdown-timer {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }

    .countdown-box {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 15px;
      padding: 20px 10px;
      position: relative;
      overflow: hidden;
    }

    .countdown-box::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, #ff9d00, transparent);
    }

    .countdown-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #ff9d00;
      line-height: 1;
      text-shadow: 0 0 20px rgba(255, 157, 0, 0.5);
      font-family: 'Poppins', monospace;
    }

    .countdown-label {
      color: #888;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-top: 8px;
    }

    .countdown-urgency {
      margin-top: 20px;
      padding: 12px 20px;
      background: rgba(255, 61, 0, 0.2);
      border-radius: 10px;
      border: 1px solid rgba(255, 61, 0, 0.3);
    }

    .countdown-urgency p {
      color: #ff6b6b;
      font-size: 0.85rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .countdown-urgency i {
      animation: pulse 1s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }

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
    @media (max-width: 1100px) {
      .limited-wrapper {
        grid-template-columns: 1fr;
        gap: 30px;
      }
      .countdown-section {
        max-width: 400px;
        margin: 0 auto;
      }
      .countdown-timer {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 768px) {
      #limited-product { padding: 100px 20px 40px; }
      .limited-card { flex-direction: column; }
      .limited-card img { width: 100%; height: 250px; }
      .limited-info { padding: 25px; }
      .limited-info h2 { font-size: 1.5rem; }
      .limited-info .product-price { font-size: 1.8rem; }
      .btn-group { flex-direction: column; }
      .btn-buy, .btn-cart { width: 100%; justify-content: center; }
      .user-name { display: none; }
      .user-display { padding: 8px 12px; }
      .countdown-timer { grid-template-columns: repeat(2, 1fr); }
      .countdown-number { font-size: 2rem; }
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
      <li><a href="admin/admin_products.php"><i class="fa fa-cog"></i> Admin</a></li>
      
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
    <div class="limited-wrapper">
      <!-- Product Card -->
      <div class="limited-card">
        <span class="limited-badge"><i class="fas fa-bolt"></i> Limited Offer</span>
        <img id="limited-img" src="" alt="Limited Shoe">
        <div class="limited-info">
          <h2 id="limited-name">Loading…</h2>
          <p class="product-desc" id="limited-desc"></p>
          <p class="product-price" id="limited-price"></p>
          
          <div class="btn-group">
            <button class="btn-buy" id="shop-now"><i class="fas fa-shopping-bag"></i> Buy Now!</button>
            <button class="btn-cart" id="limited-add-cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
          </div>
        </div>
      </div>

      <!-- Countdown Timer (Outside the card) -->
      <div class="countdown-section" id="countdown-container" style="display: none;">
        <div class="countdown-header">
          <i class="fas fa-fire-flame-curved"></i>
          <h3>Hurry Up!</h3>
          <p>Offer ends soon</p>
        </div>
        
        <div class="countdown-timer">
          <div class="countdown-box">
            <div class="countdown-number" id="days">00</div>
            <div class="countdown-label">Days</div>
          </div>
          <div class="countdown-box">
            <div class="countdown-number" id="hours">00</div>
            <div class="countdown-label">Hours</div>
          </div>
          <div class="countdown-box">
            <div class="countdown-number" id="minutes">00</div>
            <div class="countdown-label">Mins</div>
          </div>
          <div class="countdown-box">
            <div class="countdown-number" id="seconds">00</div>
            <div class="countdown-label">Secs</div>
          </div>
        </div>

        <div class="countdown-urgency">
          <p><i class="fas fa-exclamation-circle"></i> Limited stock available!</p>
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
          <a href="before.php"><img src="src/img/Black and Orange Shoe Brand Logo.png" alt="Logo"></a>
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