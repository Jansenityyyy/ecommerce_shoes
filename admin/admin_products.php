<?php
include '../php/connect.php';

// Fetch all products from all brands
$brands = ['nike', 'adidas', 'puma'];
$products = [];

foreach ($brands as $brand) {
    $sql = "SELECT *, '$brand' AS brand FROM `$brand`";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = number_format($row['price'], 2);
            $products[] = $row;
        }
    }
}

// Fetch limited products
$sql = "SELECT *, 'limited' AS brand_type FROM limited_products";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['price'] = number_format($row['price'], 2);
        $row['brand_type'] = 'limited';
        $products[] = $row;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amsterdam+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Navbar */
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
            transition: all 0.3s ease;
        }

        nav .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #ff9d00;
            font-family: 'Amsterdam One', sans-serif;
            letter-spacing: 2px;
        }

        nav .nav-links {
            list-style: none;
            display: flex;
            gap: 35px;
            margin: 0;
            padding: 0;
        }

        nav .nav-links li a {
            color: #ff9d00;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        nav .nav-links li a:hover {
            color: #fff;
        }

        nav .nav-links li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ff9d00;
            transition: width 0.3s ease;
        }

        nav .nav-links li a:hover::after {
            width: 100%;
        }

        /* Main Content */
        .dashboard-section {
            padding: 120px 30px 60px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 157, 0, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(255, 157, 0, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        h1 {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            font-weight: 700;
            color: #ff9d00;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
        }

        .stat-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.1);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.08);
            border-color: rgba(255, 157, 0, 0.3);
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.2);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: #ff9d00;
            margin-bottom: 10px;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0 5px 0;
        }

        .stat-card p {
            color: #ccc;
            font-size: 0.9rem;
        }

        /* Table Container */
        .table-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.1);
            border-radius: 20px;
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            overflow-x: auto;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 157, 0, 0.1);
        }

        th {
            background: rgba(255, 157, 0, 0.1);
            color: #ff9d00;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover {
            background: rgba(255, 157, 0, 0.05);
        }

        td {
            color: #fff;
            font-size: 0.9rem;
        }

        td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid rgba(255, 157, 0, 0.2);
            transition: all 0.3s ease;
        }

        td img:hover {
            transform: scale(1.5);
            border-color: #ff9d00;
            box-shadow: 0 5px 20px rgba(255, 157, 0, 0.4);
            z-index: 100;
            position: relative;
        }

        .description-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .brand-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .brand-nike { background: rgba(255, 107, 107, 0.2); color: #ff6b6b; }
        .brand-adidas { background: rgba(78, 205, 196, 0.2); color: #4ecdc4; }
        .brand-puma { background: rgba(121, 134, 203, 0.2); color: #7986cb; }
        .brand-limited { background: rgba(255, 157, 0, 0.2); color: #ff9d00; }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        a.button {
            padding: 8px 16px;
            background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
            color: #111;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 5px 15px rgba(255, 157, 0, 0.3);
        }

        a.button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 157, 0, 0.5);
        }

        a.button.delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        a.button.delete:hover {
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.5);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #ccc;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ff9d00;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            nav {
                padding: 15px 30px;
            }

            nav .logo {
                font-size: 1.5rem;
            }

            nav .nav-links {
                gap: 20px;
            }

            nav .nav-links li a {
                font-size: 0.9rem;
            }

            .dashboard-section {
                padding: 100px 20px 40px;
            }

            h1 {
                font-size: 2rem;
            }

            .table-container {
                padding: 20px 15px;
            }

            table {
                font-size: 0.8rem;
            }

            th, td {
                padding: 10px 8px;
            }

            .description-cell {
                max-width: 150px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        @media screen and (max-width: 480px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 8px 5px;
                font-size: 0.75rem;
            }

            td img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="../index.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="index.php"><i class="fas fa-plus"></i> Add Product</a></li>
            <li><a href="#"><i class="fas fa-list"></i> Manage</a></li>
        </ul>
    </nav>

    <div class="dashboard-section">
        <h1><i class="fas fa-tachometer-alt"></i> Product Management Dashboard</h1>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-boxes"></i>
                <h3><?= count($products) ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <h3><?= count(array_filter($products, function($p) { return isset($p['brand_type']) && $p['brand_type'] == 'limited'; })) ?></h3>
                <p>Limited Products</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-store"></i>
                <h3><?= count(array_filter($products, function($p) { return !isset($p['brand_type']); })) ?></h3>
                <p>Regular Products</p>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-container">
            <?php if(count($products) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-tag"></i> Brand</th>
                        <th><i class="fas fa-shopping-bag"></i> Product Name</th>
                        <th><i class="fas fa-dollar-sign"></i> Price</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-align-left"></i> Description</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): 
                        $brandClass = isset($product['brand_type']) && $product['brand_type'] == 'limited' 
                            ? 'brand-limited' 
                            : 'brand-' . strtolower($product['brand']);
                        $brandDisplay = isset($product['brand_type']) && $product['brand_type'] == 'limited' 
                            ? 'Limited' 
                            : ucfirst($product['brand']);
                    ?>
                    <tr>
                        <td><strong>#<?= $product['id'] ?></strong></td>
                        <td><span class="brand-badge <?= $brandClass ?>"><?= $brandDisplay ?></span></td>
                        <td><strong><?= $product['name'] ?></strong></td>
                        <td><strong>₱ <?= $product['price'] ?></strong></td>
                        <td><img src="../src/img/<?= $product['brand'] ?>/<?= $product['image'] ?>" alt="<?= $product['name'] ?>"></td>
                        <td class="description-cell" title="<?= htmlspecialchars($product['description']) ?>"><?= $product['description'] ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="button" href="edit_product.php?id=<?= $product['id'] ?>&brand=<?= isset($product['brand_type']) && $product['brand_type']=='limited' ? 'limited' : $product['brand'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a class="button delete" href="delete_product.php?id=<?= $product['id'] ?>&brand=<?= isset($product['brand_type']) && $product['brand_type']=='limited' ? 'limited' : $product['brand'] ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h2>No Products Found</h2>
                <p>Start by adding your first product!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>