<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Database connection
require_once 'php/connect.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: HomePage.php");
    exit();
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: HomePage.php");
    exit();
}

// Fetch product images (if you have a separate images table)
$images_stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY display_order");
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();
$product_images = [];
while ($img = $images_result->fetch_assoc()) {
    $product_images[] = $img['image_url'];
}
$images_stmt->close();

// If no additional images, use main product image
if (empty($product_images)) {
    $product_images = [$product['image']];
}

// Fetch related products (same category, excluding current product)
$related_stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->bind_param("si", $product['category'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$related_products = [];
while ($related = $related_result->fetch_assoc()) {
    $related_products[] = $related;
}
$related_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - SenSneaks Inc.</title>
    <link rel="stylesheet" href="src/css/before.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Navigation Styles */
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

        nav .nav-links li a {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Product Detail Styles */
        .product-container {
            max-width: 1400px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .breadcrumb {
            margin-bottom: 30px;
            color: #aaa;
            font-size: 0.95rem;
        }

        .breadcrumb a {
            color: #ff9d00;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: #fff;
        }

        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .main-image {
            width: 100%;
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .main-image img {
            width: 100%;
            height: auto;
            object-fit: contain;
            max-height: 500px;
        }

        .thumbnail-images {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .thumbnail {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: #ff9d00;
            transform: translateY(-5px);
        }

        .thumbnail.active {
            border-color: #ff9d00;
        }

        .thumbnail img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .product-info {
            color: #fff;
        }

        .product-info h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .product-price {
            font-size: 2rem;
            color: #ff9d00;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .product-options {
            margin-bottom: 30px;
        }

        .option-group {
            margin-bottom: 25px;
        }

        .option-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .option-group select {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(255, 157, 0, 0.3);
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option-group select:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(0, 0, 0, 0.7);
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-selector input {
            width: 80px;
            padding: 12px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid rgba(255, 157, 0, 0.3);
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 18px 40px;
            background: linear-gradient(135deg, #00c851, #007E33);
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 200, 81, 0.4);
        }

        .buy-now-btn {
            width: 100%;
            padding: 18px 40px;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .buy-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.4);
        }

        .product-details-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 157, 0, 0.2);
        }

        .details-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .details-header:hover {
            color: #ff9d00;
        }

        .details-header h3 {
            font-size: 1.5rem;
            margin: 0;
        }

        .details-content {
            color: #ddd;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .product-meta {
            display: flex;
            gap: 30px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 157, 0, 0.2);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #aaa;
        }

        .meta-item i {
            color: #ff9d00;
            font-size: 1.2rem;
        }

        /* Related Products */
        .related-products {
            margin-top: 80px;
        }

        .related-products h2 {
            text-align: center;
            font-size: 2rem;
            color: #fff;
            margin-bottom: 40px;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .related-card {
            background: rgba(28, 28, 28, 0.95);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .related-card:hover {
            transform: translateY(-10px);
            border-color: #ff9d00;
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.3);
        }

        .related-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 15px;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
        }

        .related-card h4 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .related-card .price {
            color: #ff9d00;
            font-size: 1.3rem;
            font-weight: 700;
        }

        /* Alert Messages */
        .alert {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 10000;
            animation: slideIn 0.5s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.95);
            color: #fff;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.95);
            color: #fff;
        }

        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .product-container {
                margin-top: 100px;
            }

            .product-info h1 {
                font-size: 2rem;
            }

            .thumbnail-images {
                grid-template-columns: repeat(3, 1fr);
            }
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
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span>Cart</span></a></li>
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
            <li><a href="login.php" class="login-link"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="product-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="before.php">Home</a> / 
            <a href="HomePage.php"><?= htmlspecialchars($product['category']) ?></a> / 
            <span><?= htmlspecialchars($product['name']) ?></span>
        </div>

        <!-- Product Detail -->
        <div class="product-detail">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <img src="<?= htmlspecialchars($product_images[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainImage">
                </div>
                
                <?php if (count($product_images) > 1): ?>
                <div class="thumbnail-images">
                    <?php foreach ($product_images as $index => $image): ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage('<?= htmlspecialchars($image) ?>', this)">
                        <img src="<?= htmlspecialchars($image) ?>" alt="Thumbnail">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>

                <!-- Product Options -->
                <div class="product-options">
                    <?php if (!empty($product['sizes'])): ?>
                    <div class="option-group">
                        <label for="size">Size</label>
                        <select id="size" name="size">
                            <?php
                            $sizes = explode(',', $product['sizes']);
                            foreach ($sizes as $size):
                            ?>
                            <option value="<?= trim($size) ?>"><?= trim($size) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['colors'])): ?>
                    <div class="option-group">
                        <label for="color">Color</label>
                        <select id="color" name="color">
                            <?php
                            $colors = explode(',', $product['colors']);
                            foreach ($colors as $color):
                            ?>
                            <option value="<?= trim($color) ?>"><?= trim($color) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="option-group">
                        <label for="quantity">Quantity</label>
                        <div class="quantity-selector">
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                            <span style="color: #aaa;"><?= $product['stock'] ?> available</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <button class="add-to-cart-btn" onclick="addToCart(<?= $product_id ?>)">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                <button class="buy-now-btn" onclick="buyNow(<?= $product_id ?>)">
                    <i class="fas fa-bolt"></i> Buy Now
                </button>

                <!-- Product Details -->
                <div class="product-details-section">
                    <div class="details-header" onclick="toggleDetails()">
                        <i class="fas fa-list-ul"></i>
                        <h3>Product Details</h3>
                        <i class="fas fa-chevron-down" id="detailsArrow" style="margin-left: auto; transition: transform 0.3s ease;"></i>
                    </div>
                    <div class="details-content" id="detailsContent">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                </div>

                <!-- Product Meta -->
                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>Category: <?= htmlspecialchars($product['category']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-star"></i>
                        <span>In Stock</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <h2><i class="fas fa-heart"></i> You May Also Like</h2>
            <div class="related-grid">
                <?php foreach ($related_products as $related): ?>
                <div class="related-card" onclick="window.location.href='product_detail.php?id=<?= $related['id'] ?>'">
                    <img src="<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                    <h4><?= htmlspecialchars($related['name']) ?></h4>
                    <div class="price">₱<?= number_format($related['price'], 2) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle Dropdown
        function toggleDropdown() {
            const userMenu = document.querySelector('.user-menu');
            userMenu.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu && !userMenu.contains(event.target)) {
                userMenu.classList.remove('active');
            }
        });

        // Change Main Image
        function changeImage(imageUrl, element) {
            document.getElementById('mainImage').src = imageUrl;
            document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
            element.classList.add('active');
        }

        // Toggle Product Details
        function toggleDetails() {
            const content = document.getElementById('detailsContent');
            const arrow = document.getElementById('detailsArrow');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Add to Cart
        async function addToCart(productId) {
            <?php if (!$isLoggedIn): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            const quantity = document.getElementById('quantity').value;
            const size = document.getElementById('size')?.value || '';
            const color = document.getElementById('color')?.value || '';

            try {
                const response = await fetch('php/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'add',
                        product_id: productId,
                        quantity: quantity,
                        size: size,
                        color: color
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert('Product added to cart!', 'success');
                } else {
                    showAlert(data.message || 'Failed to add to cart', 'error');
                }
            } catch (error) {
                showAlert('Error adding to cart', 'error');
            }
        }

        // Buy Now
        function buyNow(productId) {
            <?php if (!$isLoggedIn): ?>
                window.location.href = 'login.php';
                return;
            <?php endif; ?>

            addToCart(productId).then(() => {
                setTimeout(() => {
                    window.location.href = 'cart.php';
                }, 1000);
            });
        }

        // Show Alert
        function showAlert(message, type) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }
    </script>
</body>
</html>