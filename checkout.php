<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Redirect to login if not logged in
if (!$isLoggedIn) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

        /* User Dropdown & Cart Badge */
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
        .user-display:hover { background: rgba(255, 157, 0, 0.2); border-color: #ff9d00; color: #fff; }
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
        .user-menu.active .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
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
        
        .cart-link { position: relative; display: inline-flex; align-items: center; gap: 8px; }
        .cart-badge {
            position: absolute;
            top: -10px;
            right: -15px;
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
        }
        .cart-badge.hidden { display: none; }

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

        /* Checkout Container */
        .checkout-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .checkout-title {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .checkout-title i { color: #ff9d00; }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        /* Shipping Form */
        .shipping-section {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 157, 0, 0.15);
            border-radius: 20px;
            padding: 30px;
        }

        .shipping-section h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .shipping-section h3 i { color: #ff9d00; }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group label .required {
            color: #ff6b6b;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(255, 157, 0, 0.05);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        /* Payment Method */
        .payment-methods {
            display: grid;
            gap: 15px;
            margin-top: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 157, 0, 0.2);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option:hover {
            border-color: #ff9d00;
            background: rgba(255, 157, 0, 0.05);
        }

        .payment-option input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            accent-color: #ff9d00;
        }

        .payment-option label {
            flex: 1;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-option i {
            color: #ff9d00;
            font-size: 1.3rem;
        }

        /* Order Summary */
        .order-summary {
            background: linear-gradient(145deg, rgba(255, 157, 0, 0.1), rgba(255, 119, 0, 0.05));
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .order-summary h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .order-item:last-of-type {
            border-bottom: none;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-details h4 {
            color: #fff;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .order-item-details .brand {
            color: #ff9d00;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .order-item-details .item-price {
            color: #888;
            font-size: 0.9rem;
        }

        .summary-totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            color: #ccc;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .summary-row.total {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-row.total span:last-child {
            color: #ff9d00;
        }

        .place-order-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            border: none;
            border-radius: 30px;
            color: #111;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .place-order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.4);
        }

        .place-order-btn:disabled {
            background: #555;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #ff9d00;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            .order-summary {
                position: relative;
                top: 0;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .user-name { display: none; }
            .checkout-container { margin-top: 100px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> Products</a></li>
            <li>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="cart-badge hidden" id="cart-badge">0</span>
                </a>
            </li>
            <li class="user-menu">
                <div class="user-display" onclick="toggleDropdown()">
                    <div class="user-avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
                    <span class="user-name"><?= htmlspecialchars($username) ?></span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                    <a href="orders.php"><i class="fas fa-box"></i> My Orders</a>
                    <a href="wishlist.php"><i class="fas fa-heart"></i> My Wishlist</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Checkout Content -->
    <div class="checkout-container">
        <h1 class="checkout-title"><i class="fas fa-credit-card"></i> Checkout</h1>
        
        <div class="checkout-grid">
            <!-- Shipping Form -->
            <div class="shipping-section">
                <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
                
                <form id="checkout-form">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="full_name" id="full_name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" id="phone" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address <span class="required">*</span></label>
                        <input type="text" name="address" id="address" placeholder="Street Address, Building, Unit" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>City <span class="required">*</span></label>
                            <input type="text" name="city" id="city" required>
                        </div>
                        <div class="form-group">
                            <label>Province <span class="required">*</span></label>
                            <input type="text" name="province" id="province" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Postal Code <span class="required">*</span></label>
                        <input type="text" name="postal_code" id="postal_code" required>
                    </div>

                    <div class="form-group">
                        <label>Order Notes (Optional)</label>
                        <textarea name="notes" id="notes" placeholder="Any special instructions?"></textarea>
                    </div>

                    <h3 style="margin-top: 30px;"><i class="fas fa-wallet"></i> Payment Method</h3>
                    
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label for="cod">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Cash on Delivery</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="gcash" value="gcash">
                            <label for="gcash">
                                <i class="fas fa-mobile-alt"></i>
                                <span>GCash</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="card" value="card">
                            <label for="card">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit/Debit Card</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                
                <div id="order-items">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading order...</p>
                    </div>
                </div>

                <div class="summary-totals" id="summary-totals" style="display: none;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span id="shipping">₱150.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="total">₱0.00</span>
                    </div>
                </div>

                <button class="place-order-btn" id="place-order-btn" disabled>
                    <i class="fas fa-lock"></i> Place Order
                </button>
            </div>
        </div>
    </div>

    <script>
        const SHIPPING_FEE = 150;
        let cartItems = [];

        // Load cart items
        document.addEventListener('DOMContentLoaded', () => {
            loadCartItems();
            updateCartBadge();
        });

        async function loadCartItems() {
            try {
                const res = await fetch('php/cart.php?action=get');
                cartItems = await res.json();

                if (cartItems.length === 0) {
                    window.location.href = 'cart.php';
                    return;
                }

                displayOrderSummary();
                document.getElementById('place-order-btn').disabled = false;
            } catch (err) {
                console.error('Error loading cart:', err);
                alert('Error loading cart items');
            }
        }

        function displayOrderSummary() {
            const container = document.getElementById('order-items');
            let html = '';
            let subtotal = 0;

            cartItems.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                html += `
                    <div class="order-item">
                        <img src="src/img/${item.image}" alt="${item.name}">
                        <div class="order-item-details">
                            <h4>${item.name}</h4>
                            <div class="brand">${item.brand}</div>
                            <div class="item-price">₱${item.price.toLocaleString('en-PH', {minimumFractionDigits: 2})} × ${item.quantity}</div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;

            const total = subtotal + SHIPPING_FEE;
            document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('total').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('summary-totals').style.display = 'block';
        }

        // Place Order
        document.getElementById('place-order-btn').addEventListener('click', async () => {
            const form = document.getElementById('checkout-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = document.getElementById('place-order-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            const formData = new FormData(form);
            formData.append('action', 'place_order');
            formData.append('items', JSON.stringify(cartItems));

            // Debug: Log what we're sending
            console.log('Sending order data:', {
                items: cartItems,
                form: Object.fromEntries(formData)
            });

            try {
                const res = await fetch('php/process_order.php', {
                    method: 'POST',
                    body: formData
                });

                // Check if response is OK
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                // Get response text first to check if it's valid JSON
                const responseText = await res.text();
                console.log('Response text:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response was:', responseText);
                    throw new Error('Server returned invalid JSON. Check console for details.');
                }

                if (data.success) {
                    window.location.href = 'order_success.php?order=' + data.order_number;
                } else {
                    alert(data.message || 'Error placing order');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
                }
            } catch (err) {
                console.error('Error placing order:', err);
                alert('Error placing order: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
            }
        });

        // Update Cart Badge
        async function updateCartBadge() {
            try {
                const res = await fetch('php/cart.php?action=count');
                const data = await res.json();
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    const count = data.count || 0;
                    badge.textContent = count;
                    badge.classList.toggle('hidden', count === 0);
                }
            } catch (err) {}
        }

        function toggleDropdown() {
            document.querySelector('.user-menu').classList.toggle('active');
        }

        document.addEventListener('click', function(e) {
            const menu = document.querySelector('.user-menu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });
    </script>
</body>
</html>