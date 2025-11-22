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
    <title>Welcome to SenSneaks Inc.</title>
    <link rel="stylesheet" href="src/css/before.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
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

        /* Cart Link with Badge */
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
            animation: pulse 2s infinite;
        }
        .cart-badge.hidden { display: none; }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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

        /* Nav links with icons */
        nav .nav-links li a {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        nav .nav-links li a i {
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .user-name { display: none; }
            .user-display { padding: 8px 12px; }
            .dropdown-menu { right: -10px; }
            nav .nav-links { gap: 15px; }
            nav .nav-links li a span { display: none; }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="before.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> <span>Products</span></a></li>
            <li>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> <span>Cart</span>
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
            <li><a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
<?php endif; ?>

        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Elevate Your Style,</h1>
                <h2>Define Your Trend.</h2>
                <p><span>SenSneaks Inc.</span> offers the latest styles and timeless elegance to help you express your
                    unique style. Fashion is a statement.</p>
                <a href="HomePage.php" class="cta-button">
                    Explore Now <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="hero-image">
                <img src="src/img/b.png" alt="Premium Sneakers">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <h2><i class="fas fa-award"></i> Why Choose SenSneaks?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-star"></i>
                <h3>Premium Quality</h3>
                <p>Handpicked collection of the finest footwear from top brands worldwide.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Delivery</h3>
                <p>Get your orders delivered quickly and safely right to your doorstep.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Shopping</h3>
                <p>Shop with confidence using our secure payment and data protection systems.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Our dedicated team is always ready to assist you with any questions.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-tag"></i>
                <h3>Best Prices</h3>
                <p>Competitive pricing and exclusive deals on authentic sneakers and footwear.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-sync-alt"></i>
                <h3>Easy Returns</h3>
                <p>Hassle-free 30-day return policy for your complete satisfaction and peace of mind.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <div class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3><i class="fas fa-mobile-alt"></i> Download Our App</h3>
                    <p>Download App for Android and iOS</p>
                    <div class="app-logo">
                        <img src="src/img/d1.png" alt="Play Store">
                        <img src="src/img/d2.png" alt="App Store">
                    </div>
                </div>
                <div class="footer-col-2">
                    <a href="before.php"><img src="src/img/Black and Orange Shoe Brand Logo.png" alt="SenSneaks Logo"></a>
                    <p>SenSneaks Inc.—stepping up your style with premium comfort, trendsetting designs, and exclusive
                        footwear for every occasion.</p>
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
                        <li><a href="https://www.facebook.com/profile.php?id=61573238416249">
                                <i class="fa-brands fa-facebook"></i> Facebook</a></li>
                        <li><a href="https://www.instagram.com/JansenMark04">
                                <i class="fa-brands fa-instagram"></i> Instagram</a></li>
                        <li><a href="https://www.youtube.com/JansenMark04">
                                <i class="fa-brands fa-youtube"></i> YouTube</a></li>
                        <li><a href="https://www.tiktok.com/@JansenMark04">
                                <i class="fa-brands fa-tiktok"></i> TikTok</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">© 2025 SenSneaks Inc. All rights reserved.</p>
        </div>
    </div>

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

        // Update Cart Badge
        async function updateCartBadge() {
            try {
                const res = await fetch('php/cart.php?action=count');
                const data = await res.json();
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    const count = data.count || 0;
                    badge.textContent = count;
                    if (count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch (err) {
                // User not logged in, hide badge
                const badge = document.getElementById('cart-badge');
                if (badge) badge.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadge();
        });
    </script>

</body>

</html>