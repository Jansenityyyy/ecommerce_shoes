<?php
session_start();
include 'php/connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=orders.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

        .orders-container {
            max-width: 1000px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .page-title {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .page-title i { color: #ff9d00; }

        .order-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 157, 0, 0.15);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .order-card:hover {
            border-color: rgba(255, 157, 0, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-number {
            color: #ff9d00;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .order-date {
            color: #888;
            font-size: 0.9rem;
        }

        .order-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-processing {
            background: rgba(33, 150, 243, 0.2);
            color: #2196f3;
            border: 1px solid rgba(33, 150, 243, 0.3);
        }

        .status-shipped {
            background: rgba(156, 39, 176, 0.2);
            color: #9c27b0;
            border: 1px solid rgba(156, 39, 176, 0.3);
        }

        .status-delivered {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            color: #ccc;
            font-size: 0.9rem;
        }

        .detail-item label {
            color: #888;
            display: block;
            margin-bottom: 5px;
        }

        .order-total {
            color: #ff9d00;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 5rem;
            color: #444;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #fff;
            margin-bottom: 10px;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .orders-container { margin-top: 100px; }
            .order-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .order-details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Navbar (same as other pages) -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> Products</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li class="user-menu">
                <div class="user-display" onclick="toggleDropdown()">
                    <div class="user-avatar" style="width:35px;height:35px;border-radius:50%;background:linear-gradient(135deg,#ff9d00,#ff7700);display:flex;align-items:center;justify-content:center;font-weight:700;color:#111;font-size:1rem;">
                        <?= strtoupper(substr($username, 0, 1)) ?>
                    </div>
                    <span class="user-name" style="font-weight:600;font-size:0.95rem;"><?= htmlspecialchars($username) ?></span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="dropdown-menu" style="position:absolute;top:120%;right:0;background:rgba(28,28,28,0.98);backdrop-filter:blur(10px);border:1px solid rgba(255,157,0,0.2);border-radius:15px;padding:10px 0;min-width:200px;box-shadow:0 10px 30px rgba(0,0,0,0.5);opacity:0;visibility:hidden;transform:translateY(-10px);transition:all 0.3s;z-index:1000;">
                    <a href="profile.php" style="display:flex;align-items:center;gap:12px;padding:12px 20px;color:#fff;text-decoration:none;transition:all 0.3s;font-size:0.95rem;"><i class="fas fa-user"></i> My Profile</a>
                    <a href="orders.php" style="display:flex;align-items:center;gap:12px;padding:12px 20px;color:#fff;text-decoration:none;transition:all 0.3s;font-size:0.95rem;"><i class="fas fa-box"></i> My Orders</a>
                    <a href="settings.php" style="display:flex;align-items:center;gap:12px;padding:12px 20px;color:#fff;text-decoration:none;transition:all 0.3s;font-size:0.95rem;"><i class="fas fa-cog"></i> Settings</a>
                    <div style="height:1px;background:rgba(255,157,0,0.2);margin:8px 0;"></div>
                    <a href="php/logout.php" style="display:flex;align-items:center;gap:12px;padding:12px 20px;color:#fff;text-decoration:none;transition:all 0.3s;font-size:0.95rem;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </nav>

    <div class="orders-container">
        <h1 class="page-title"><i class="fas fa-box"></i> My Orders</h1>

        <?php if (mysqli_num_rows($orders) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number"><?= htmlspecialchars($order['order_number']) ?></div>
                            <div class="order-date">
                                <i class="fas fa-calendar"></i>
                                <?= date('F d, Y', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                        <span class="order-status status-<?= $order['order_status'] ?>">
                            <?= ucfirst($order['order_status']) ?>
                        </span>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <label><i class="fas fa-wallet"></i> Payment Method</label>
                            <?= strtoupper($order['payment_method']) ?>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-money-bill"></i> Total Amount</label>
                            <span class="order-total">₱<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-map-marker-alt"></i> Shipping To</label>
                            <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?>
                        </div>
                        <div class="detail-item">
                            <label><i class="fas fa-info-circle"></i> Payment Status</label>
                            <?= ucfirst($order['payment_status']) ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No Orders Yet</h3>
                <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                <a href="HomePage.php"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDropdown() {
            document.querySelector('.user-menu').classList.toggle('active');
        }
        document.addEventListener('click', function(e) {
            const menu = document.querySelector('.user-menu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });
        
        const style = document.createElement('style');
        style.textContent = '.user-menu.active .dropdown-arrow { transform: rotate(180deg); } .user-menu.active .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }';
        document.head.appendChild(style);
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>