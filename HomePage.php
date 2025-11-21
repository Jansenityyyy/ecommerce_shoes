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
  <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    /* Toast Notification */
    .toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      padding: 15px 25px;
      border-radius: 10px;
      color: #fff;
      font-weight: 600;
      z-index: 9999;
      animation: slideIn 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .toast.success { background: linear-gradient(135deg, #27ae60, #2ecc71); }
    .toast.error { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .toast.info { background: linear-gradient(135deg, #ff9d00, #ff7700); color: #111; }
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    /* Cart Badge */
    .cart-badge {
      background: #e74c3c;
      color: #fff;
      font-size: 0.7rem;
      padding: 2px 6px;
      border-radius: 10px;
      margin-left: 5px;
    }
    /* User menu styles */
    .user-menu { position: relative; }
    .user-display {
      display: flex; align-items: center; gap: 10px;
      color: #ff9d00; cursor: pointer; padding: 8px 15px;
      border-radius: 25px; background: rgba(255, 157, 0, 0.1);
      border: 1px solid rgba(255, 157, 0, 0.2); transition: all 0.3s ease;
    }
    .user-display:hover { background: rgba(255, 157, 0, 0.2); border-color: #ff9d00; color: #fff; }
    .user-avatar {
      width: 35px; height: 35px; border-radius: 50%;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; color: #111; font-size: 1rem;
    }
    .dropdown-menu {
      position: absolute; top: 120%; right: 0;
      background: rgba(28, 28, 28, 0.98); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 157, 0, 0.2); border-radius: 15px;
      padding: 10px 0; min-width: 200px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000;
    }
    .user-menu.active .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-menu a {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 20px; color: #fff; text-decoration: none; transition: all 0.3s ease;
    }
    .dropdown-menu a:hover { background: rgba(255, 157, 0, 0.1); color: #ff9d00; }
    .dropdown-menu a i { width: 20px; text-align: center; color: #ff9d00; }
    .dropdown-divider { height: 1px; background: rgba(255, 157, 0, 0.2); margin: 8px 0; }
    .login-link {
      display: flex; align-items: center; gap: 8px; padding: 8px 20px;
      border-radius: 25px; background: rgba(255, 157, 0, 0.1);
      border: 1px solid rgba(255, 157, 0, 0.2); transition: all 0.3s ease;
    }
    .login-link:hover { background: rgba(255, 157, 0, 0.2); border-color: #ff9d00; }
    /* Product Card Add to Cart */
    .product-card .add-cart-btn {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      color: #111;
      border: none;
      border-radius: 25px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .product-card .add-cart-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(255, 157, 0, 0.4);
    }
    /* Limited Product Buttons */
    .limited-buttons {
      display: flex;
      gap: 15px;
      margin-top: 15px;
      flex-wrap: wrap;
    }
    .limited-buttons button {
      padding: 15px 30px;
      border: none;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-buy {
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      color: #111;
      box-shadow: 0 10px 30px rgba(255, 157, 0, 0.3);
    }
    .btn-buy:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(255, 157, 0, 0.5);
    }
    .btn-cart {
      background: transparent;
      color: #ff9d00;
      border: 2px solid #ff9d00 !important;
    }
    .btn-cart:hover {
      background: rgba(255, 157, 0, 0.1);
      color: #fff;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
      <li><a href="HomePage.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="#other-products"><i class="fas fa-shopping-bag"></i> Products</a></li>
      <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart <span class="cart-badge" id="cart-count" style="display:none;">0</span></a></li>
      
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
              <div class="countdown-box"><div class="countdown-number" id="days">00</div><div class="countdown-text">Days</div></div>
              <div class="countdown-box"><div class="countdown-number" id="hours">00</div><div class="countdown-text">Hours</div></div>
              <div class="countdown-box"><div class="countdown-number" id="minutes">00</div><div class="countdown-text">Minutes</div></div>
              <div class="countdown-box"><div class="countdown-number" id="seconds">00</div><div class="countdown-text">Seconds</div></div>
            </div>
          </div>
          
          <div class="limited-buttons">
            <button class="btn-buy" id="shop-now"><i class="fas fa-shopping-bag"></i> Buy Now!</button>
            <button class="btn-cart" id="add-limited-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
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

  <!-- Footer -->
  <div class="footer" id="contact">
    <div class="container">
      <div class="row">
        <div class="footer-col-1">
          <h3>Download Our App</h3>
          <p>Download App for Android and iOS</p>
          <div class="app-logo">
            <img src="src/img/d1.png" alt="Play Store">
            <img src="src/img/d2.png" alt="App Store">
          </div>
        </div>
        <div class="footer-col-2">
          <a href="#"><img src="src/img/Black and Orange Shoe Brand Logo.png" alt="Logo"></a>
          <p>SenSneaks Inc.—stepping up your style with premium comfort and exclusive footwear.</p>
        </div>
        <div class="footer-col-3">
          <h3>Useful Links</h3>
          <ul>
            <li><i class="fa-solid fa-ticket"></i> Coupons</li>
            <li><i class="fa-solid fa-undo"></i> Return Policy</li>
            <li><i class="fa-solid fa-comment"></i> Feedback</li>
          </ul>
        </div>
        <div class="footer-col-4">
          <h3>Follow us</h3>
          <ul>
            <li><a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a></li>
            <li><a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a></li>
            <li><a href="#"><i class="fa-brands fa-tiktok"></i> TikTok</a></li>
          </ul>
        </div>
      </div>
      <hr>
      <p class="copyright">© 2025 SenSneaks Inc. All rights reserved.</p>
    </div>
  </div>

  <script>
    // Toast notification
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i> ${message}`;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
    }

    // Add to cart function
    function addToCart(productId, productName, productPrice, productImage, productBrand) {
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('product_name', productName);
      formData.append('product_price', productPrice);
      formData.append('product_image', productImage);
      formData.append('product_brand', productBrand);
      
      fetch('php/add_to_cart.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToast(data.message, 'success');
          updateCartCount();
        } else {
          if (data.redirect) {
            showToast(data.message, 'info');
            setTimeout(() => window.location.href = data.redirect, 1500);
          } else {
            showToast(data.message, 'error');
          }
        }
      })
      .catch(err => {
        console.error('Error:', err);
        showToast('Failed to add to cart', 'error');
      });
    }

    // Update cart count
    function updateCartCount() {
      fetch('php/get_cart.php')
        .then(res => res.json())
        .then(data => {
          const badge = document.getElementById('cart-count');
          if (data.success && data.total_items > 0) {
            badge.textContent = data.total_items;
            badge.style.display = 'inline';
          } else {
            badge.style.display = 'none';
          }
        });
    }

    // Store limited product data
    let limitedProduct = null;

    // Fetch Limited Product
    fetch('php/fetch_limited.php')
      .then(res => res.json())
      .then(product => {
        if(product){
          limitedProduct = product;
          document.getElementById('limited-img').src = `src/img/${product.image}`;
          document.getElementById('limited-name').innerText = product.name;
          document.getElementById('limited-desc').innerText = product.description;
          const formattedPrice = parseFloat(product.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
          document.getElementById('limited-price').innerText = `₱${formattedPrice}`;
          
          // Add to cart button for limited product
          document.getElementById('add-limited-cart').onclick = function() {
            addToCart(product.id, product.name, product.price, product.image, product.brand || 'limited');
          };
          
          if(product.end_date) {
            startCountdown(product.end_date);
          }
        } else {
          document.getElementById('limited-product').style.display = 'none';
        }
      });

    // Fetch Other Products
    const brands = ['nike', 'adidas', 'puma'];
    const productList = document.getElementById('productList');

    brands.forEach(brand => {
      fetch(`php/fetch_products.php?brand=${brand}`)
        .then(res => res.json())
        .then(data => {
          data.forEach(p => {
            const productImg = `src/img/${brand}/${p.image}`;
            const formattedPrice = parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            const card = document.createElement('div');
            card.className = 'product-card';
            card.innerHTML = `
              <img src="${productImg}" alt="${p.name}">
              <h3>${p.name}</h3>
              <p>₱${formattedPrice}</p>
              <button class="add-cart-btn" onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.price}, '${brand}/${p.image}', '${brand}')">
                <i class="fas fa-shopping-cart"></i> Add to Cart
              </button>
            `;
            productList.appendChild(card);
          });
        });
    });

    // Countdown timer function
    function startCountdown(endDate) {
      const container = document.getElementById('countdown-container');
      container.style.display = 'block';
      
      const timer = setInterval(() => {
        const now = new Date().getTime();
        const end = new Date(endDate).getTime();
        const distance = end - now;
        
        if (distance < 0) {
          clearInterval(timer);
          container.innerHTML = '<div class="countdown-label" style="color:#e74c3c;">Offer has ended!</div>';
          return;
        }
        
        document.getElementById('days').textContent = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
        document.getElementById('hours').textContent = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
        document.getElementById('minutes').textContent = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        document.getElementById('seconds').textContent = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
      }, 1000);
    }

    // Toggle dropdown
    function toggleDropdown() {
      document.querySelector('.user-menu')?.classList.toggle('active');
    }

    document.addEventListener('click', function(e) {
      const userMenu = document.querySelector('.user-menu');
      if (userMenu && !userMenu.contains(e.target)) {
        userMenu.classList.remove('active');
      }
    });

    // Update cart count on load
    updateCartCount();
  </script>
</body>
</html>