<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Database connection
require_once 'php/config.php';

// Initialize messages
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update Profile Information
    if (isset($_POST['update_profile'])) {
        $new_username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        if (!empty($new_username)) {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $new_username, $email, $phone, $address, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username;
                $username = $new_username;
                $success_message = "Profile updated successfully!";
            } else {
                $error_message = "Error updating profile.";
            }
            $stmt->close();
        }
    }
    
    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Error changing password.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
            $stmt->close();
        } else {
            $error_message = "New passwords do not match.";
        }
    }
    
    // Update Notification Preferences
    if (isset($_POST['update_notifications'])) {
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
        $promo_notifications = isset($_POST['promo_notifications']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE users SET email_notifications = ?, sms_notifications = ?, promo_notifications = ? WHERE id = ?");
        $stmt->bind_param("iiii", $email_notifications, $sms_notifications, $promo_notifications, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Notification preferences updated!";
        } else {
            $error_message = "Error updating preferences.";
        }
        $stmt->close();
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - SenSneaks Inc.</title>
    <link rel="stylesheet" href="src/css/before.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Reuse navigation styles from before.php */
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

        /* Settings Page Styles */
        .settings-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .settings-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .settings-header h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .settings-header p {
            color: #aaa;
            font-size: 1.1rem;
        }

        .settings-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            border-bottom: 2px solid rgba(255, 157, 0, 0.2);
            overflow-x: auto;
        }

        .tab-button {
            padding: 15px 30px;
            background: transparent;
            border: none;
            color: #aaa;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
        }

        .tab-button:hover {
            color: #ff9d00;
        }

        .tab-button.active {
            color: #ff9d00;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ff9d00;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .settings-card {
            background: rgba(28, 28, 28, 0.95);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
        }

        .settings-card h2 {
            color: #ff9d00;
            margin-bottom: 25px;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #fff;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 1rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 157, 0, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(0, 0, 0, 0.7);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #ff9d00;
        }

        .checkbox-group label {
            color: #fff;
            margin: 0;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 157, 0, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            color: #fff;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 68, 68, 0.4);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.2);
            border: 1px solid #2ecc71;
            color: #2ecc71;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }

        .info-box {
            background: rgba(255, 157, 0, 0.1);
            border: 1px solid rgba(255, 157, 0, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-box p {
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-box i {
            color: #ff9d00;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .settings-container {
                margin-top: 100px;
            }

            .settings-card {
                padding: 25px;
            }

            .settings-header h1 {
                font-size: 2rem;
            }

            .settings-tabs {
                gap: 10px;
            }

            .tab-button {
                padding: 12px 20px;
                font-size: 0.9rem;
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

    <!-- Settings Container -->
    <div class="settings-container">
        <div class="settings-header">
            <h1><i class="fas fa-cog"></i> Account Settings</h1>
            <p>Manage your account preferences and security settings</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- Settings Tabs -->
        <div class="settings-tabs">
            <button class="tab-button active" onclick="switchTab('profile')">
                <i class="fas fa-user"></i> Profile
            </button>
            <button class="tab-button" onclick="switchTab('security')">
                <i class="fas fa-lock"></i> Security
            </button>
            <button class="tab-button" onclick="switchTab('notifications')">
                <i class="fas fa-bell"></i> Notifications
            </button>
            <button class="tab-button" onclick="switchTab('privacy')">
                <i class="fas fa-shield-alt"></i> Privacy
            </button>
        </div>

        <!-- Profile Tab -->
        <div id="profile-tab" class="tab-content active">
            <div class="settings-card">
                <h2><i class="fas fa-user-edit"></i> Profile Information</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address"><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Security Tab -->
        <div id="security-tab" class="tab-content">
            <div class="settings-card">
                <h2><i class="fas fa-key"></i> Change Password</h2>
                <div class="info-box">
                    <p><i class="fas fa-info-circle"></i> Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.</p>
                </div>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn-primary">
                        <i class="fas fa-lock"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="notifications-tab" class="tab-content">
            <div class="settings-card">
                <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
                <form method="POST" action="">
                    <div class="checkbox-group">
                        <input type="checkbox" id="email_notifications" name="email_notifications" 
                               <?= ($user_data['email_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <label for="email_notifications">
                            <strong>Email Notifications</strong><br>
                            <small style="color: #aaa;">Receive order updates and promotions via email</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="sms_notifications" name="sms_notifications" 
                               <?= ($user_data['sms_notifications'] ?? 0) ? 'checked' : '' ?>>
                        <label for="sms_notifications">
                            <strong>SMS Notifications</strong><br>
                            <small style="color: #aaa;">Get text messages for important updates</small>
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="promo_notifications" name="promo_notifications" 
                               <?= ($user_data['promo_notifications'] ?? 1) ? 'checked' : '' ?>>
                        <label for="promo_notifications">
                            <strong>Promotional Offers</strong><br>
                            <small style="color: #aaa;">Receive exclusive deals and discount alerts</small>
                        </label>
                    </div>
                    
                    <button type="submit" name="update_notifications" class="btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </form>
            </div>
        </div>

        <!-- Privacy Tab -->
        <div id="privacy-tab" class="tab-content">
            <div class="settings-card">
                <h2><i class="fas fa-shield-alt"></i> Privacy & Data</h2>
                <div class="info-box">
                    <p><i class="fas fa-info-circle"></i> Your privacy is important to us. Review and manage how we handle your data.</p>
                </div>
                
                <div style="margin: 30px 0;">
                    <h3 style="color: #fff; margin-bottom: 15px;">Data Management</h3>
                    <p style="color: #aaa; margin-bottom: 20px;">Request a copy of your personal data or permanently delete your account.</p>
                    
                    <button class="btn-primary" style="margin-right: 15px;">
                        <i class="fas fa-download"></i> Download My Data
                    </button>
                    
                    <button class="btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt"></i> Delete Account
                    </button>
                </div>
            </div>
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

        // Switch between tabs
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.closest('.tab-button').classList.add('active');
        }

        // Confirm account deletion
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data, orders, and preferences. Are you absolutely sure?')) {
                    // Redirect to account deletion handler
                    window.location.href = 'php/delete_account.php';
                }
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>