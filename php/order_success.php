<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$order_number = $_GET['order'] ?? '';
if (empty($order_number)) {
    header('Location: HomePage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

        .success-container {
            max-width: 600px;
            margin: 150px auto 50px;
            padding: 0 20px;
            text-align: center;
        }

        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #4caf50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease;
        }

        .success-icon i {
            font-size: 4rem;
            color: #fff;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-container h1 {
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .success-container p {
            color: #aaa;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .order-number {
            background: rgba(255, 157, 0, 0.1);
            border: 1px solid rgba(255, 157, 0, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
        }

        .order-number label {
            color: #888;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 8px;
        }

        .order-number .number {
            color: #ff9d00;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 157, 0, 0.4);
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

        .features {
            margin-top: 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .feature-item {
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            border: 1px solid rgba(255, 157, 0, 0.1);
        }

        .feature-item i {
            font-size: 2rem;
            color: #ff9d00;
            margin-bottom: 10px;
        }

        .feature-item h4 {
            color: #fff;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .feature-item p {
            color: #888;
            font-size: 0.8rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .success-container {
                margin-top: 120px;
            }
            .success-container h1 {
                font-size: 2rem;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Order Confirmed!</h1>
        <p>Thank you for your order. We've received your order and will process it shortly. You'll receive a confirmation email with your order details.</p>

        <div class="order-number">
            <label>Your Order Number</label>
            <div class="number"><?= htmlspecialchars($order_number) ?></div>
        </div>

        <div class="action-buttons">
            <a href="orders.php" class="btn btn-primary">
                <i class="fas fa-box"></i> View My Orders
            </a>
            <a href="HomePage.php" class="btn btn-secondary">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>

        <div class="features">
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <h4>Fast Delivery</h4>
                <p>3-5 business days</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h4>Secure Payment</h4>
                <p>100% protected</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h4>24/7 Support</h4>
                <p>Always here to help</p>
            </div>
        </div>
    </div>

</body>
</html>