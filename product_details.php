<?php
session_start();
require_once 'php/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Get product ID and brand from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$brand = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';

// Fetch product details
$table_name = strtolower($brand);
$query = "SELECT * FROM $table_name WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: HomePage.php');
    exit();
}

$product = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - SenSneaks Inc.</title>

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #0d0d0d;
            color: #fff;
            min-height: 100vh;
        }

        /* Navbar Styles */
        nav {
            background: rgba(17, 17, 17, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 157, 0, 0.1);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff9d00;
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
            padding: 8px 15px;
            border-radius: 8px;
        }

        .nav-links a:hover {
            color: #ff9d00;
            background: rgba(255, 157, 0, 0.1);
        }

        .nav-links a i {
            color: #ff9d00;
        }

        /* Product Detail Container */
        .product-detail-container {
            max-width: 1300px;
            margin: 80px auto;
            padding: 0 50px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            background: rgba(255, 157, 0, 0.1);
            border: 1px solid rgba(255, 157, 0, 0.3);
            border-radius: 10px;
            color: #ff9d00;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .back-button:hover {
            background: rgba(255, 157, 0, 0.2);
            transform: translateX(-5px);
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            background: rgba(255, 255, 255, 0.03);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(255, 157, 0, 0.1);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        }

        /* Image Section */
        .product-image-section {
            position: relative;
        }

        .product-image-container {
            position: relative;
            background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 500px;
        }

        .product-image-container img {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .product-image-container:hover img {
            transform: scale(1.05);
        }

        .wishlist-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }

        .wishlist-btn:hover {
            background: #fff;
            transform: scale(1.1);
        }

        .wishlist-btn i {
            font-size: 1.6rem;
            color: #ff9d00;
        }

        .wishlist-btn.active i {
            color: #ff0000;
        }

        /* Info Section */
        .product-info-section {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .brand-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 157, 0, 0.2);
            border: 1px solid rgba(255, 157, 0, 0.4);
            border-radius: 20px;
            color: #ff9d00;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: fit-content;
        }

        .product-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin: 10px 0;
        }

        .product-price {
            font-size: 3rem;
            font-weight: 700;
            color: #ff9d00;
            margin: 15px 0;
        }

        .product-description {
            color: #aaa;
            font-size: 1.05rem;
            line-height: 1.8;
            padding: 20px 0;
            border-top: 1px solid rgba(255, 157, 0, 0.1);
            border-bottom: 1px solid rgba(255, 157, 0, 0.1);
        }

        .product-details-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 20px 0;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 157, 0, 0.1);
        }

        .detail-item i {
            font-size: 1.3rem;
            color: #ff9d00;
            width: 30px;
            text-align: center;
        }

        .detail-item-label {
            color: #888;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .detail-item-value {
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            margin-left: auto;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 18px 30px;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            box-shadow: 0 6px 25px rgba(255, 157, 0, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(255, 157, 0, 0.6);
        }

        .btn-secondary {
            background: transparent;
            color: #ff9d00;
            border: 2px solid #ff9d00;
        }

        .btn-secondary:hover {
            background: rgba(255, 157, 0, 0.1);
            transform: translateY(-3px);
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

        .notification-toast.show {
            transform: translateX(0);
        }

        .notification-toast.success i {
            color: #4caf50;
            font-size: 1.3rem;
        }

        .notification-toast.error i {
            color: #ff6b6b;
            font-size: 1.3rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                padding: 30px;
                gap: 40px;
            }

            .product-title {
                font-size: 2rem;
            }

            .product-price {
                font-size: 2.2rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }

            .product-detail-container {
                padding: 0 20px;
                margin: 40px auto;
            }

            .product-detail-grid {
                padding: 20px;
            }

            .product-image-container {
                min-height: 300px;
                padding: 20px;
            }
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
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span>Cart</span></a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="php/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Product Details -->
    <div class="product-detail-container">
        <a href="HomePage.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>

        <div class="product-detail-grid">
            <!-- Image Section -->
            <div class="product-image-section">
                <div class="product-image-container">
                    <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>, '<?= $brand ?>')">
                        <i class="far fa-heart"></i>
                    </button>
                    <img src="src/img/<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>

            <!-- Info Section -->
            <div class="product-info-section">
                <span class="brand-badge">
                    <i class="fas fa-tag"></i> <?= strtoupper($brand) ?>
                </span>

                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="product-price">
                    ₱<?= number_format($product['price'], 2) ?>
                </div>

                <p class="product-description">
                    <?= htmlspecialchars($product['description']) ?>
                </p>

                <div class="product-details-list">
                    <div class="detail-item">
                        <i class="fas fa-barcode"></i>
                        <span class="detail-item-label">Product ID</span>
                        <span class="detail-item-value">#<?= $product['id'] ?></span>
                    </div>

                    <div class="detail-item">
                        <i class="fas fa-building"></i>
                        <span class="detail-item-label">Brand</span>
                        <span class="detail-item-value"><?= strtoupper($brand) ?></span>
                    </div>

                    <div class="detail-item">
                        <i class="fas fa-check-circle"></i>
                        <span class="detail-item-label">Availability</span>
                        <span class="detail-item-value" style="color: #4caf50;">In Stock</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="buyNow(<?= $product['id'] ?>, '<?= $brand ?>')">
                        <i class="fas fa-shopping-bag"></i> Buy Now
                    </button>
                    <button class="btn btn-secondary" onclick="addToCart(<?= $product['id'] ?>, '<?= $brand ?>')">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check wishlist status on load
        window.addEventListener('DOMContentLoaded', async () => {
            const productId = <?= $product['id'] ?>;
            const brand = '<?= $brand ?>';

            try {
                const res = await fetch(`php/check_wishlist.php?product_id=${productId}&brand=${brand}`);
                const data = await res.json();

                if (data.success && data.in_wishlist) {
                    const btn = document.querySelector('.wishlist-btn');
                    const icon = btn.querySelector('i');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.classList.add('active');
                }
            } catch (err) {
                console.error('Error checking wishlist:', err);
            }
        });

        // Toggle Wishlist
        async function toggleWishlist(productId, brand) {
            try {
                const formData = new FormData();
                formData.append('action', 'toggle');
                formData.append('product_id', productId);
                formData.append('brand', brand);

                const res = await fetch('php/manage_wishlist.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    const btn = document.querySelector('.wishlist-btn');
                    const icon = btn.querySelector('i');

                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('active');
                        showNotification('Added to wishlist!', 'success');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('active');
                        showNotification('Removed from wishlist', 'success');
                    }
                } else {
                    showNotification(data.message, 'error');
                    if (data.message === 'Please login first') {
                        setTimeout(() => {
                            window.location.href = 'login.php?redirect=product_details.php?id=' + productId + '&brand=' + brand;
                        }, 1500);
                    }
                }
            } catch (err) {
                console.error('Error:', err);
                showNotification('Error updating wishlist', 'error');
            }
        }

        // Add to Cart
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
                } else {
                    showNotification(data.message, 'error');
                    setTimeout(() => {
                        window.location.href = 'login.php?redirect=product_details.php?id=' + productId + '&brand=' + brand;
                    }, 1500);
                }
            } catch (err) {
                console.error('Error:', err);
                showNotification('Error adding to cart', 'error');
            }
        }

        // Buy Now
        async function buyNow(productId, brand) {
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
                    window.location.href = 'checkout.php';
                } else {
                    showNotification(data.message, 'error');
                    setTimeout(() => {
                        window.location.href = 'login.php?redirect=product_details.php?id=' + productId + '&brand=' + brand;
                    }, 1500);
                }
            } catch (err) {
                console.error('Error:', err);
                showNotification('Please login first', 'error');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 1500);
            }
        }

        // Show Notification
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
    </script>
</body>

</html>