<?php
session_start();
include 'php/connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: php/logout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SenSneaks Inc.</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }

        /* Navbar Styles */
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
        .user-display:hover { background: rgba(255, 157, 0, 0.2); border-color: #ff9d00; color: #fff; }
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
        .user-menu.active .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
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

        .cart-link { position: relative; display: inline-flex; align-items: center; gap: 8px; }
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
        }
        .cart-badge.hidden { display: none; }

        /* Profile Container */
        .profile-container {
            max-width: 900px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: #111;
            margin: 0 auto 20px;
            box-shadow: 0 10px 40px rgba(255, 157, 0, 0.3);
        }

        .profile-header h1 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .profile-header .member-since {
            color: #888;
            font-size: 0.9rem;
        }

        /* Profile Cards */
        .profile-grid {
            display: grid;
            gap: 25px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 157, 0, 0.15);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s;
        }

        .profile-card:hover {
            border-color: rgba(255, 157, 0, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-header h3 {
            color: #fff;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            color: #ff9d00;
        }

        .edit-btn {
            background: rgba(255, 157, 0, 0.1);
            border: 1px solid rgba(255, 157, 0, 0.3);
            color: #ff9d00;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-btn:hover {
            background: rgba(255, 157, 0, 0.2);
            border-color: #ff9d00;
        }

        /* Form Styles */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #888;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .info-value {
            color: #fff;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(255, 157, 0, 0.05);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 157, 0, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #888;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        /* Edit Forms (Hidden by default) */
        .edit-form {
            display: none;
        }

        .edit-form.active {
            display: block;
        }

        .view-mode {
            display: block;
        }

        .view-mode.hidden {
            display: none;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        .alert i {
            font-size: 1.3rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 157, 0, 0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }

        .stat-card i {
            font-size: 2.5rem;
            color: #ff9d00;
            margin-bottom: 10px;
        }

        .stat-card h4 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #888;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .profile-container { margin-top: 100px; }
            .user-name { display: none; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="HomePage.php"><i class="fas fa-shopping-bag"></i> Products</a></li>
            <li>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i> Cart
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

    <!-- Profile Content -->
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-large"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p class="member-since">
                <i class="fas fa-calendar"></i> 
                Member since <?= date('F Y', strtotime($user['created_at'])) ?>
            </p>
        </div>

        <!-- Alert Messages -->
        <div id="alert-container"></div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h4 id="total-orders">0</h4>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h4 id="cart-items">0</h4>
                <p>Items in Cart</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <h4>0</h4>
                <p>Wishlist Items</p>
            </div>
        </div>

        <!-- Profile Cards -->
        <div class="profile-grid">
            <!-- Account Information -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> Account Information</h3>
                    <button class="edit-btn" onclick="toggleEdit('account')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>

                <!-- View Mode -->
                <div id="account-view" class="view-mode">
                    <div class="info-row">
                        <span class="info-label">Username</span>
                        <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account Status</span>
                        <span class="info-value" style="color: #4caf50;"><i class="fas fa-check-circle"></i> Active</span>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form id="account-edit" class="edit-form" onsubmit="updateAccount(event)">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleEdit('account')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password & Security -->
            <div class="profile-card">
                <div class="card-header">
                    <h3><i class="fas fa-lock"></i> Password & Security</h3>
                    <button class="edit-btn" onclick="toggleEdit('password')">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>

                <!-- View Mode -->
                <div id="password-view" class="view-mode">
                    <div class="info-row">
                        <span class="info-label">Password</span>
                        <span class="info-value">••••••••</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Last Changed</span>
                        <span class="info-value"><?= date('F d, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form id="password-edit" class="edit-form" onsubmit="updatePassword(event)">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" minlength="6" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Password
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleEdit('password')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle Edit Mode
        function toggleEdit(section) {
            const viewMode = document.getElementById(`${section}-view`);
            const editMode = document.getElementById(`${section}-edit`);
            
            viewMode.classList.toggle('hidden');
            editMode.classList.toggle('active');
        }

        // Show Alert
        function showAlert(message, type = 'success') {
            const container = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            container.appendChild(alert);
            
            setTimeout(() => alert.remove(), 5000);
        }

        // Update Account
        async function updateAccount(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'update_account');

            try {
                const res = await fetch('php/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (err) {
                showAlert('Error updating account', 'error');
            }
        }

        // Update Password
        async function updatePassword(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            // Validate password match
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                showAlert('Passwords do not match', 'error');
                return;
            }

            formData.append('action', 'update_password');

            try {
                const res = await fetch('php/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    e.target.reset();
                    toggleEdit('password');
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (err) {
                showAlert('Error updating password', 'error');
            }
        }

        // Load Stats
        async function loadStats() {
            try {
                // Get total orders
                const ordersRes = await fetch('php/get_stats.php?type=orders');
                const ordersData = await ordersRes.json();
                if (ordersData.success) {
                    document.getElementById('total-orders').textContent = ordersData.count;
                }

                // Get cart items
                const cartRes = await fetch('php/cart.php?action=count');
                const cartData = await cartRes.json();
                document.getElementById('cart-items').textContent = cartData.count || 0;
            } catch (err) {
                console.error('Error loading stats:', err);
            }
        }

        // Update Cart Badge
        async function updateCartBadge() {
            try {
                const res = await fetch('php/cart.php?action=count');
                const data = await res.json();
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    const count = data.count || 0;
                    badge.textContent = count;
                    badge.classList.toggle('hidden', count === 0);
                }
            } catch (err) {}
        }

        // Dropdown Toggle
        function toggleDropdown() {
            document.querySelector('.user-menu').classList.toggle('active');
        }

        document.addEventListener('click', function(e) {
            const menu = document.querySelector('.user-menu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadStats();
            updateCartBadge();
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>