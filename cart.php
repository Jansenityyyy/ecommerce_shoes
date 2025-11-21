<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Redirect to login if not logged in
if (!$isLoggedIn) {
    header("Location: login.php?error=login_required");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart - SenSneaks Inc.</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    .cart-section {
      padding: 120px 20px 80px;
      min-height: 100vh;
    }
    .cart-container {
      max-width: 1200px;
      margin: 0 auto;
    }
    .cart-title {
      text-align: center;
      font-size: 2.5rem;
      color: #ff9d00;
      margin-bottom: 40px;
      font-weight: 700;
    }
    .cart-content {
      display: grid;
      grid-template-columns: 1fr 350px;
      gap: 30px;
    }
    .cart-items {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255, 157, 0, 0.1);
      border-radius: 20px;
      padding: 25px;
    }
    .cart-item {
      display: flex;
      gap: 20px;
      padding: 20px 0;
      border-bottom: 1px solid rgba(255, 157, 0, 0.1);
      animation: fadeIn 0.5s ease;
    }
    .cart-item:last-child {
      border-bottom: none;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .cart-item-image {
      width: 120px;
      height: 120px;
      border-radius: 15px;
      object-fit: cover;
      border: 2px solid rgba(255, 157, 0, 0.2);
    }
    .cart-item-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .cart-item-name {
      font-size: 1.2rem;
      font-weight: 600;
      color: #fff;
      margin-bottom: 5px;
    }
    .cart-item-brand {
      font-size: 0.85rem;
      color: #ff9d00;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .cart-item-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: #ff9d00;
    }
    .cart-item-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      justify-content: space-between;
    }
    .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
      background: rgba(255,255,255,0.05);
      border-radius: 10px;
      padding: 5px;
    }
    .qty-btn {
      width: 35px;
      height: 35px;
      border: none;
      border-radius: 8px;
      background: rgba(255, 157, 0, 0.2);
      color: #ff9d00;
      cursor: pointer;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .qty-btn:hover {
      background: #ff9d00;
      color: #111;
    }
    .qty-value {
      font-size: 1.1rem;
      font-weight: 600;
      min-width: 30px;
      text-align: center;
    }
    .remove-btn {
      background: rgba(231, 76, 60, 0.2);
      color: #e74c3c;
      border: none;
      padding: 8px 15px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }
    .remove-btn:hover {
      background: #e74c3c;
      color: #fff;
    }
    .cart-summary {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255, 157, 0, 0.1);
      border-radius: 20px;
      padding: 25px;
      height: fit-content;
      position: sticky;
      top: 100px;
    }
    .summary-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: #fff;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid rgba(255, 157, 0, 0.1);
      color: #ccc;
    }
    .summary-row.total {
      border-bottom: none;
      font-size: 1.3rem;
      font-weight: 700;
      color: #ff9d00;
      padding-top: 20px;
    }
    .checkout-btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
      color: #111;
      border: none;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.3s ease;
    }
    .checkout-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(255, 157, 0, 0.4);
    }
    .continue-btn {
      width: 100%;
      padding: 12px;
      background: transparent;
      color: #ff9d00;
      border: 2px solid #ff9d00;
      border-radius: 50px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 15px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .continue-btn:hover {
      background: rgba(255, 157, 0, 0.1);
    }
    .empty-cart {
      text-align: center;
      padding: 60px 20px;
    }
    .empty-cart i {
      font-size: 5rem;
      color: rgba(255, 157, 0, 0.3);
      margin-bottom: 20px;
    }
    .empty-cart h3 {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }
    .empty-cart p {
      color: #ccc;
      margin-bottom: 20px;
    }
    .loading {
      text-align: center;
      padding: 40px;
      color: #ff9d00;
    }
    .loading i {
      font-size: 2rem;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
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
    /* Responsive */
    @media (max-width: 900px) {
      .cart-content { grid-template-columns: 1fr; }
      .cart-summary { position: static; }
    }
    @media (max-width: 600px) {
      .cart-item { flex-direction: column; text-align: center; }
      .cart-item-image { width: 100%; max-width: 200px; height: auto; margin: 0 auto; }
      .cart-item-actions { align-items: center; flex-direction: row; justify-content: center; gap: 15px; margin-top: 15px; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav>
    <div class="logo">SenSneaks Inc.</div>
    <ul class="nav-links">
      <li><a href="HomePage.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="HomePage.php#other-products"><i class="fas fa-shopping-bag"></i> Products</a></li>
      <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart <span id="cart-badge"></span></a></li>
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
    </ul>
  </nav>

  <section class="cart-section">
    <div class="cart-container">
      <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> My Shopping Cart</h1>
      
      <div class="cart-content">
        <div class="cart-items" id="cart-items">
          <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Loading your cart...</p>
          </div>
        </div>
        
        <div class="cart-summary" id="cart-summary">
          <h2 class="summary-title"><i class="fas fa-receipt"></i> Order Summary</h2>
          <div class="summary-row">
            <span>Items (<span id="total-items">0</span>)</span>
            <span>₱<span id="subtotal">0.00</span></span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>₱<span id="shipping">0.00</span></span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>₱<span id="grand-total">0.00</span></span>
          </div>
          <button class="checkout-btn" id="checkout-btn">
            <i class="fas fa-credit-card"></i> Proceed to Checkout
          </button>
          <a href="HomePage.php" class="continue-btn">
            <i class="fas fa-arrow-left"></i> Continue Shopping
          </a>
        </div>
      </div>
    </div>
  </section>

  <script>
    // Load cart items
    function loadCart() {
      fetch('php/get_cart.php')
        .then(res => res.json())
        .then(data => {
          const cartContainer = document.getElementById('cart-items');
          
          if (!data.success || data.items.length === 0) {
            cartContainer.innerHTML = `
              <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added anything yet!</p>
                <a href="HomePage.php" class="checkout-btn" style="display:inline-block;width:auto;padding:15px 30px;">
                  <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
              </div>
            `;
            updateSummary(0, 0);
            return;
          }
          
          let html = '';
          data.items.forEach(item => {
            html += `
              <div class="cart-item" data-id="${item.id}">
                <img src="src/img/${item.brand}/${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                  <span class="cart-item-brand">${item.brand}</span>
                  <h3 class="cart-item-name">${item.name}</h3>
                  <p class="cart-item-price">₱${parseFloat(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                </div>
                <div class="cart-item-actions">
                  <div class="quantity-control">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 'decrease')">
                      <i class="fas fa-minus"></i>
                    </button>
                    <span class="qty-value">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 'increase')">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  <button class="remove-btn" onclick="updateQuantity(${item.id}, 'remove')">
                    <i class="fas fa-trash"></i> Remove
                  </button>
                </div>
              </div>
            `;
          });
          
          cartContainer.innerHTML = html;
          updateSummary(data.total_amount, data.total_items);
        })
        .catch(err => {
          console.error('Error loading cart:', err);
          document.getElementById('cart-items').innerHTML = '<p style="text-align:center;color:#e74c3c;">Error loading cart</p>';
        });
    }

    // Update cart summary
    function updateSummary(subtotal, items) {
      const shipping = subtotal > 0 ? (subtotal >= 5000 ? 0 : 150) : 0;
      const total = subtotal + shipping;
      
      document.getElementById('total-items').textContent = items;
      document.getElementById('subtotal').textContent = subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
      document.getElementById('shipping').textContent = shipping.toLocaleString('en-PH', {minimumFractionDigits: 2});
      document.getElementById('grand-total').textContent = total.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    // Update quantity
    function updateQuantity(cartId, action) {
      const formData = new FormData();
      formData.append('cart_id', cartId);
      formData.append('action', action);
      
      fetch('php/update_cart.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          loadCart();
        } else {
          alert(data.message);
        }
      })
      .catch(err => console.error('Error:', err));
    }

    // Toggle dropdown
    function toggleDropdown() {
      document.querySelector('.user-menu').classList.toggle('active');
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
      const userMenu = document.querySelector('.user-menu');
      if (userMenu && !userMenu.contains(e.target)) {
        userMenu.classList.remove('active');
      }
    });

    // Load cart on page load
    loadCart();
  </script>
</body>
</html>