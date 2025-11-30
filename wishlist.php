<?php
session_start();
include 'php/connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=wishlist.php');
    exit;
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

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

        /* Wishlist Container */
        .wishlist-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .wishlist-title {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .wishlist-title i { color: #ff9d00; }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .wishlist-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 157, 0, 0.15);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            animation: fadeInUp 0.6s ease;
        }

        .wishlist-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 157, 0, 0.3);
            box-shadow: 0 15px 40px rgba(255, 157, 0, 0.25);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .wishlist-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
            background: #fff;
            padding: 10px;
            transition: transform 0.3s ease;
        }

        .wishlist-card:hover img {
            transform: scale(1.05);
        }

        .wishlist-card h3 {
            color: #fff;
            font-size: 1.2rem;
            margin-bottom: 8px;
            min-height: 50px;
        }

        .wishlist-card .brand {
            color: #ff9d00;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .wishlist-card .price {
            color: #ff9d00;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .wishlist-actions {
            display: flex;
            gap: 10px;
        }

        .add-cart-btn, .remove-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .add-cart-btn {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            box-shadow: 0 4px 15px rgba(255, 157, 0, 0.3);
        }

        .add-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 157, 0, 0.5);
        }

        .remove-btn {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .remove-btn:hover {
            background: rgba(255, 0, 0, 0.4);
            color: #fff;
        }

        .empty-wishlist {
            text-align: center;
            padding: 80px 20px;
            color: #888;
        }

        .empty-wishlist i {
            font-size: 5rem;
            color: #444;
            margin-bottom: 20px;
        }

        .empty-wishlist h3 {
            color: #fff;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .empty-wishlist p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .empty-wishlist a {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-wishlist a:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.4);
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #ff9d00;
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
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

        @media (max-width: 768px) {
            .wishlist-container { margin-top: 100px; }
            .user-name { display: none; }
            .wishlist-grid { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
            .wishlist-actions { flex-direction: column; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="LandingPage.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> <span>Products</span></a></li>
            <li>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> <span>Cart</span>
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

    <!-- Wishlist Content -->
    <div class="wishlist-container">
        <h1 class="wishlist-title"><i class="fas fa-heart"></i> My Wishlist</h1>
        
        <div class="wishlist-grid" id="wishlist-grid">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading your wishlist...</p>
            </div>
        </div>
    </div>

    <script>
        // Load wishlist on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadWishlist();
            updateCartBadge();
        });

        // Fetch and display wishlist items
        async function loadWishlist() {
            try {
                const res = await fetch('php/get_wishlist.php');
                const items = await res.json();
                
                const container = document.getElementById('wishlist-grid');
                
                if (!items.success || items.data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-wishlist" style="grid-column: 1/-1;">
                            <i class="fas fa-heart-broken"></i>
                            <h3>Your wishlist is empty</h3>
                            <p>Start adding products you love!</p>
                            <a href="HomePage.php">
                                <i class="fas fa-shopping-bag"></i> Browse Products
                            </a>
                        </div>
                    `;
                    return;
                }

                let html = '';
                items.data.forEach(item => {
                    const formattedPrice = item.price.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    html += `
                        <div class="wishlist-card" data-wishlist-id="${item.wishlist_id}">
                            <img src="src/img/${item.image}" alt="${item.name}" onerror="this.src='src/img/placeholder.png'">
                            <div class="brand">${item.brand}</div>
                            <h3>${item.name}</h3>
                            <p class="price">₱${formattedPrice}</p>
                            <div class="wishlist-actions">
                                <button class="add-cart-btn" onclick="addToCart(${item.product_id}, '${item.brand}')">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <button class="remove-btn" onclick="removeFromWishlist(${item.wishlist_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

            } catch (err) {
                console.error('Error loading wishlist:', err);
                document.getElementById('wishlist-grid').innerHTML = `
                    <div class="empty-wishlist" style="grid-column: 1/-1;">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Error loading wishlist</h3>
                        <p>Please try again later</p>
                    </div>
                `;
            }
        }

        // Add to cart
        async function addToCart(productId, brand) {
            try {
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', productId);
                formData.append('brand', brand);
                formData.append('quantity', 1);

                const res = await fetch('php/cart.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    showNotification('Added to cart!', 'success');
                    updateCartBadge();
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
            } catch (err) {
                console.error('Error adding to cart:', err);
                showNotification('Error adding to cart', 'error');
            }
        }

        // Remove from wishlist
        async function removeFromWishlist(wishlistId) {
            if (!confirm('Remove this item from wishlist?')) return;

            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('wishlist_id', wishlistId);

                const res = await fetch('php/manage_wishlist.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    showNotification('Removed from wishlist', 'success');
                    loadWishlist();
                } else {
                    showNotification(data.message || 'Error removing item', 'error');
                }
            } catch (err) {
                console.error('Error removing from wishlist:', err);
                showNotification('Error removing item', 'error');
            }
        }

        // Update cart badge
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
                console.error('Error updating cart badge:', err);
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const existing = document.querySelector('.notification-toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = `notification-toast ${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
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
    </script>
</body>
</html>