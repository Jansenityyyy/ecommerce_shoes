<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Redirect to login if not logged in
if (!$isLoggedIn) {
    header('Location: login.php?redirect=cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

        /* Cart Badge */
        .cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
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

        /* Cart Container */
        .cart-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }
        .cart-title {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cart-title i { color: #ff9d00; }
        .cart-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        @media (max-width: 900px) {
            .cart-content { grid-template-columns: 1fr; }
        }
        .cart-items {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            background: #fff;
        }
        .item-details { flex: 1; }
        .item-details h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .item-details .brand {
            color: #ff9d00;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .item-details .price {
            color: #ff9d00;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .item-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            padding: 5px 15px;
        }
        .quantity-control button {
            background: none;
            border: none;
            color: #ff9d00;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
        }
        .quantity-control button:hover { color: #fff; }
        .quantity-control span {
            color: #fff;
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }
        .remove-btn {
            background: rgba(255,0,0,0.2);
            border: none;
            color: #ff6b6b;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        .remove-btn:hover {
            background: rgba(255,0,0,0.4);
            color: #fff;
        }
        .cart-summary {
            background: linear-gradient(145deg, rgba(255,157,0,0.1), rgba(255,119,0,0.05));
            border: 1px solid rgba(255,157,0,0.2);
            border-radius: 15px;
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .cart-summary h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            color: #ccc;
            margin-bottom: 12px;
        }
        .summary-row.total {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .summary-row.total span:last-child { color: #ff9d00; }
        .checkout-btn {
            width: 100%;
            padding: 15px;
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
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255,157,0,0.3);
        }
        .checkout-btn:disabled {
            background: #555;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }
        .empty-cart i {
            font-size: 5rem;
            color: #444;
            margin-bottom: 20px;
        }
        .empty-cart h3 {
            color: #fff;
            margin-bottom: 10px;
        }
        .empty-cart a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
        }
        .loading {
            text-align: center;
            padding: 50px;
            color: #ff9d00;
        }
        .loading i { font-size: 2rem; }

        @media (max-width: 768px) {
            .user-name { display: none; }
            .cart-container { margin-top: 100px; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="HomePage.php#other-products"><i class="fas fa-shopping-bag"></i> Products</a></li>
            <li>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="cart-badge hidden" id="cart-badge">0</span>
                </a>
            </li>
            <li><a href="admin/admin_products.php"><i class="fa fa-cog"></i> Admin</a></li>
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

    <!-- Cart Content -->
    <div class="cart-container">
        <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
        
        <div class="cart-content">
            <div class="cart-items" id="cart-items">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading your cart...</p>
                </div>
            </div>
            
            <div class="cart-summary">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">₱0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="shipping">₱0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="total">₱0.00</span>
                </div>
                <button class="checkout-btn" id="checkout-btn" disabled>
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </button>
            </div>
        </div>
    </div>

    <script>
        const SHIPPING_FEE = 150;

        // Load cart on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadCart();
            updateCartBadge();
        });

        // Fetch and display cart items
        async function loadCart() {
            try {
                const res = await fetch('php/cart.php?action=get');
                const items = await res.json();
                
                const container = document.getElementById('cart-items');
                
                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Your cart is empty</h3>
                            <p>Looks like you haven't added anything yet.</p>
                            <a href="HomePage.php#other-products">
                                <i class="fas fa-shopping-bag"></i> Start Shopping
                            </a>
                        </div>
                    `;
                    document.getElementById('checkout-btn').disabled = true;
                    return;
                }

                let html = '';
                let subtotal = 0;

                items.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    const formattedPrice = item.price.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    html += `
                        <div class="cart-item" data-cart-id="${item.cart_id}">
                            <img src="src/img/${item.image}" alt="${item.name}" onerror="this.src='src/img/placeholder.png'">
                            <div class="item-details">
                                <span class="brand">${item.brand}</span>
                                <h3>${item.name}</h3>
                                <p class="price">₱${formattedPrice}</p>
                                <div class="item-actions">
                                    <div class="quantity-control">
                                        <button onclick="updateQty(${item.cart_id}, ${item.quantity - 1})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span>${item.quantity}</span>
                                        <button onclick="updateQty(${item.cart_id}, ${item.quantity + 1})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button class="remove-btn" onclick="removeItem(${item.cart_id})">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
                updateSummary(subtotal);
                document.getElementById('checkout-btn').disabled = false;

            } catch (err) {
                console.error('Error loading cart:', err);
            }
        }

        // Update order summary
        function updateSummary(subtotal) {
            const shipping = subtotal > 0 ? SHIPPING_FEE : 0;
            const total = subtotal + shipping;

            document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('shipping').textContent = '₱' + shipping.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            document.getElementById('total').textContent = '₱' + total.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Update quantity
        async function updateQty(cartId, newQty) {
            try {
                const formData = new FormData();
                formData.append('action', 'update');
                formData.append('cart_id', cartId);
                formData.append('quantity', newQty);

                await fetch('php/cart.php', {
                    method: 'POST',
                    body: formData
                });

                loadCart();
                updateCartBadge();
            } catch (err) {
                console.error('Error updating quantity:', err);
            }
        }

        // Remove item
        async function removeItem(cartId) {
            if (!confirm('Remove this item from cart?')) return;

            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('cart_id', cartId);

                await fetch('php/cart.php', {
                    method: 'POST',
                    body: formData
                });

                loadCart();
                updateCartBadge();
            } catch (err) {
                console.error('Error removing item:', err);
            }
        }

        // Update cart badge count
        async function updateCartBadge() {
            try {
                const res = await fetch('php/cart.php?action=count');
                const data = await res.json();
                const badge = document.getElementById('cart-badge');
                const count = data.count || 0;
                badge.textContent = count;
                if (count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (err) {
                console.error('Error getting cart count:', err);
            }
        }

        // Dropdown toggle
        function toggleDropdown() {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu) {
                userMenu.classList.toggle('active');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.querySelector('.user-menu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });

        // Checkout button - THIS IS THE IMPORTANT PART
        document.getElementById('checkout-btn').addEventListener('click', function() {
            console.log('Checkout button clicked!'); // Debug log
            window.location.href = 'checkout.php';
        });
    </script>
</body>
</html>