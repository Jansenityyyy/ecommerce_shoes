<?php
include '../php/connect.php';

// Get statistics
$stats = [];

// Total Users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$stats['total_users'] = mysqli_fetch_assoc($result)['count'];

// Total Products
$total_products = 0;
$brands = ['nike', 'adidas', 'puma'];
foreach ($brands as $brand) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM `$brand`");
    $total_products += mysqli_fetch_assoc($result)['count'];
}
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM limited_products");
$total_products += mysqli_fetch_assoc($result)['count'];
$stats['total_products'] = $total_products;

// Total Orders
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = mysqli_fetch_assoc($result)['count'];

// Total Revenue
$result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders");
$stats['total_revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Orders Today
$today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = '$today'");
$stats['orders_today'] = mysqli_fetch_assoc($result)['count'];

// Revenue Today
$result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = '$today'");
$stats['revenue_today'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Pending Orders
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'");
$stats['pending_orders'] = mysqli_fetch_assoc($result)['count'];

// Total Items in All Carts
$result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart");
$stats['total_cart_items'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Recent Orders
$recent_orders = [];
$result = mysqli_query($conn, "SELECT o.*, u.username 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.id 
                                ORDER BY o.created_at DESC 
                                LIMIT 10");
while ($row = mysqli_fetch_assoc($result)) {
    $recent_orders[] = $row;
}

// Top Customers
$top_customers = [];
$result = mysqli_query($conn, "SELECT u.username, u.email, 
                                COUNT(o.id) as order_count, 
                                SUM(o.total_amount) as total_spent
                                FROM users u
                                JOIN orders o ON u.id = o.user_id
                                GROUP BY u.id
                                ORDER BY total_spent DESC
                                LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    $top_customers[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SenSneaks Inc.</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: #fff;
            min-height: 100vh;
        }
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background: rgba(28, 28, 28, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.5);
            z-index: 1000;
        }
        nav .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #ff9d00;
            font-family: 'Amsterdam One', sans-serif;
        }
        nav .nav-links {
            list-style: none;
            display: flex;
            gap: 35px;
        }
        nav .nav-links li a {
            color: #ff9d00;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        nav .nav-links li a:hover { color: #fff; }
        
        .dashboard-container {
            padding: 120px 30px 60px;
        }
        h1 {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            color: #ff9d00;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1400px;
            margin: 0 auto 50px;
        }
        .stat-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            border-color: #ff9d00;
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.3);
        }
        .stat-box i {
            font-size: 3rem;
            color: #ff9d00;
            margin-bottom: 15px;
        }
        .stat-box h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .stat-box p {
            color: #aaa;
            font-size: 0.95rem;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .content-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.15);
            border-radius: 20px;
            padding: 30px;
        }
        .content-box h2 {
            color: #ff9d00;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }
        .order-item, .customer-item {
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-item:hover, .customer-item:hover {
            background: rgba(255, 157, 0, 0.1);
        }
        @media (max-width: 968px) {
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="../LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="index.php"><i class="fas fa-plus"></i> Add Product</a></li>
            <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        </ul>
    </nav>

    <div class="dashboard-container">
        <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>

        <div class="stats-grid">
            <div class="stat-box">
                <i class="fas fa-dollar-sign"></i>
                <h3>₱<?= number_format($stats['total_revenue'], 2) ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-shopping-cart"></i>
                <h3><?= $stats['total_orders'] ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-box">
                <a href="users.php"><i class="fas fa-users"></i>
                <h3><?= $stats['total_users'] ?></h3>
                <p>Total Users</p></a>
            </div>
            <div class="stat-box">
                <i class="fas fa-box"></i>
                <h3><?= $stats['total_products'] ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-calendar-day"></i>
                <h3>₱<?= number_format($stats['revenue_today'], 2) ?></h3>
                <p>Revenue Today</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-receipt"></i>
                <h3><?= $stats['orders_today'] ?></h3>
                <p>Orders Today</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-clock"></i>
                <h3><?= $stats['pending_orders'] ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-shopping-basket"></i>
                <h3><?= $stats['total_cart_items'] ?></h3>
                <p>Items in Carts</p>
            </div>
        </div>

        <div class="content-grid">
            <div class="content-box">
                <h2><i class="fas fa-receipt"></i> Recent Orders</h2>
                <?php foreach($recent_orders as $order): ?>
                <div class="order-item">
                    <div>
                        <strong><?= $order['order_number'] ?></strong><br>
                        <small style="color: #aaa;"><?= $order['username'] ?></small>
                    </div>
                    <div style="text-align: right;">
                        <strong style="color: #ff9d00;">₱<?= number_format($order['total_amount'], 2) ?></strong><br>
                        <small style="color: #aaa;"><?= date('M d, Y', strtotime($order['created_at'])) ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="content-box">
                <h2><i class="fas fa-crown"></i> Top Customers</h2>
                <?php foreach($top_customers as $customer): ?>
                <div class="customer-item">
                    <div>
                        <strong><?= htmlspecialchars($customer['username']) ?></strong><br>
                        <small style="color: #aaa;"><?= $customer['order_count'] ?> orders</small>
                    </div>
                    <strong style="color: #ff9d00;">₱<?= number_format($customer['total_spent'], 2) ?></strong>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>