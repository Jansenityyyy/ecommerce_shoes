<?php
include '../php/connect.php';

if(!isset($_GET['id'], $_GET['brand'])) exit("Invalid request.");

$id = intval($_GET['id']);
$brand = $_GET['brand'];

if($brand == 'limited'){
    $table = 'limited_products';
} else {
    $table = in_array($brand, ['nike','adidas','puma']) ? $brand : '';
}

if(!$table) exit("Invalid brand.");

$result = mysqli_query($conn, "SELECT * FROM `$table` WHERE id=$id");
$product = mysqli_fetch_assoc($result);

if(!$product) exit("Product not found.");

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $price = floatval(str_replace(['₱',',',' '], '', $_POST['price']));
    $description = $_POST['description'];

    $imgPath = $product['image'];

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $imgName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];
        $targetDir = "../src/img/$brand/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        move_uploaded_file($tmpName, $targetDir.$imgName);
        $imgPath = $imgName;
    }

    $sql = "UPDATE `$table` SET name=?, price=?, image=?, description=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdssi", $name, $price, $imgPath, $description, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - SenSneaks Admin</title>
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
        .edit-section {
            padding: 120px 20px 80px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .edit-section::before {
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

        .edit-container {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            transition: all 0.4s ease;
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 1;
        }

        .edit-container:hover {
            transform: translateY(-10px);
            background: rgba(255,255,255,0.08);
            border-color: rgba(255, 157, 0, 0.3);
            box-shadow: 0 20px 60px rgba(255, 157, 0, 0.2);
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

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 700;
            color: #ff9d00;
            letter-spacing: 1px;
        }

        .brand-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .brand-nike { background: rgba(255, 107, 107, 0.2); color: #ff6b6b; }
        .brand-adidas { background: rgba(78, 205, 196, 0.2); color: #4ecdc4; }
        .brand-puma { background: rgba(121, 134, 203, 0.2); color: #7986cb; }
        .brand-limited { background: rgba(255, 157, 0, 0.2); color: #ff9d00; }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            display: block;
            margin: 10px 0 8px 0;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
        }

        label i {
            margin-right: 8px;
            color: #ff9d00;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255, 157, 0, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            margin-bottom: 5px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 15px rgba(255, 157, 0, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        input[type="file"]::file-selector-button {
            padding: 8px 16px;
            background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
            color: #111;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        input[type="file"]::file-selector-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 157, 0, 0.4);
        }

        .current-image {
            margin: 15px 0 25px 0;
            text-align: center;
        }

        .current-image p {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .current-image img {
            max-width: 250px;
            width: 100%;
            border-radius: 15px;
            border: 2px solid rgba(255, 157, 0, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .current-image img:hover {
            transform: scale(1.05);
            border-color: #ff9d00;
            box-shadow: 0 15px 40px rgba(255, 157, 0, 0.3);
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        button, .back-button {
            flex: 1;
            padding: 15px 35px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1.1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button {
            background: linear-gradient(135deg, #ff9d00 0%, #ff7700 100%);
            color: #111;
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.3);
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #fff 0%, #ff9d00 100%);
            transition: left 0.4s ease;
            z-index: -1;
        }

        button:hover::before {
            left: 0;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 157, 0, 0.5);
        }

        .back-button {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 157, 0, 0.3);
            box-shadow: none;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ff9d00;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 157, 0, 0.2);
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

            .edit-section {
                padding: 100px 20px 60px;
            }

            .edit-container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.7rem;
            }

            .button-group {
                flex-direction: column;
            }

            button, .back-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="../LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="admin.php"><i class="fas fa-plus"></i> Add Product</a></li>
            <li><a href="admin_products.php"><i class="fas fa-list"></i> Manage</a></li>
        </ul>
    </nav>

    <div class="edit-section">
        <div class="edit-container">
            <h1><i class="fas fa-edit"></i> Edit Product</h1>
            
            <?php 
            $brandClass = $brand == 'limited' ? 'brand-limited' : 'brand-' . strtolower($brand);
            $brandDisplay = $brand == 'limited' ? 'Limited Edition' : ucfirst($brand);
            ?>
            <div style="text-align: center;">
                <span class="brand-badge <?= $brandClass ?>">
                    <i class="fas fa-tag"></i> <?= $brandDisplay ?>
                </span>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <label><i class="fas fa-shopping-bag"></i> Product Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

                <label><i class="fas fa-dollar-sign"></i> Price:</label>
                <input type="text" name="price" value="₱ <?= number_format($product['price'],2) ?>" required>

                <label><i class="fas fa-align-left"></i> Description:</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

                <label><i class="fas fa-image"></i> Change Image (Optional):</label>
                <input type="file" name="image" accept="image/*">

                <div class="current-image">
                    <p><i class="fas fa-eye"></i> Current Image:</p>
                    <img src="../src/img/<?= $brand ?>/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>

                <div class="button-group">
                    <a href="admin_products.php" class="back-button">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>