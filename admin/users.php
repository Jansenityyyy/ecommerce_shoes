<?php
include '../php/connect.php';

// Fetch all users
$sql = "SELECT u.*, 
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
        (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_spent,
        (SELECT COUNT(*) FROM cart WHERE user_id = u.id) as cart_items,
        (SELECT COUNT(*) FROM wishlist WHERE user_id = u.id) as wishlist_items
        FROM users u 
        ORDER BY u.created_at DESC";
$result = mysqli_query($conn, $sql);
$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Get statistics
$stats = [
    'total_users' => count($users),
    'users_today' => 0,
    'users_this_month' => 0,
    'active_users' => 0
];

$today = date('Y-m-d');
$this_month = date('Y-m');

foreach ($users as $user) {
    if (date('Y-m-d', strtotime($user['created_at'])) == $today) {
        $stats['users_today']++;
    }
    if (date('Y-m', strtotime($user['created_at'])) == $this_month) {
        $stats['users_this_month']++;
    }
    if ($user['total_orders'] > 0) {
        $stats['active_users']++;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SenSneaks Admin</title>
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
            display: flex;
            align-items: center;
            gap: 8px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

        /* Search and Filter Bar */
        .search-filter-bar {
            max-width: 1400px;
            margin: 0 auto 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 30px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(255,255,255,0.08);
        }

        .search-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff9d00;
        }

        .filter-select {
            padding: 12px 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255, 157, 0, 0.2);
            border-radius: 30px;
            color: #fff;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #ff9d00;
            background: rgba(255,255,255,0.08);
        }

        .filter-select option {
            background: #1a1a1a;
            color: #fff;
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

        /* User Avatar */
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #111;
            font-size: 1.1rem;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }

        .status-inactive {
            background: rgba(158, 158, 158, 0.2);
            color: #9e9e9e;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-view {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            color: #fff;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.5);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ff9d00, #ff7700);
            color: #111;
            box-shadow: 0 5px 15px rgba(255, 157, 0, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 157, 0, 0.5);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
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

        /* Alert Messages */
        .alert {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 10001;
            animation: slideDown 0.5s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.95);
            border: 1px solid #4caf50;
            color: #fff;
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.95);
            border: 1px solid #f44336;
            color: #fff;
        }

        @keyframes slideDown {
            from {
                top: -50px;
                opacity: 0;
            }
            to {
                top: 100px;
                opacity: 1;
            }
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: rgba(28, 28, 28, 0.98);
            border: 1px solid rgba(255, 157, 0, 0.3);
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #ff9d00;
            font-size: 1.8rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            color: #ff9d00;
            transform: rotate(90deg);
        }

        .user-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 157, 0, 0.1);
        }

        .user-detail-row:last-child {
            border-bottom: none;
        }

        .user-detail-label {
            color: #aaa;
            font-weight: 600;
        }

        .user-detail-value {
            color: #fff;
            font-weight: 500;
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

            .action-buttons {
                flex-direction: column;
            }

            .search-filter-bar {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
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
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">SenSneaks Inc.</div>
        <ul class="nav-links">
            <li><a href="../LandingPage.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="index.php"><i class="fas fa-plus"></i> Add Product</a></li>
            <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        </ul>
    </nav>

    <div class="dashboard-section">
        <?php if(isset($_GET['deleted']) && $_GET['deleted'] == 'success'): ?>
        <div class="alert alert-success" id="successAlert">
            <i class="fas fa-check-circle"></i> User deleted successfully!
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['deleted']) && $_GET['deleted'] == 'error'): ?>
        <div class="alert alert-error" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i> Error deleting user. Please try again.
        </div>
        <?php endif; ?>

        <h1><i class="fas fa-users"></i> User Management</h1>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?= $stats['total_users'] ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-plus"></i>
                <h3><?= $stats['users_today'] ?></h3>
                <p>Registered Today</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-alt"></i>
                <h3><?= $stats['users_this_month'] ?></h3>
                <p>This Month</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check"></i>
                <h3><?= $stats['active_users'] ?></h3>
                <p>Active Buyers</p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-filter-bar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search users by name, email..." onkeyup="filterUsers()">
                <i class="fas fa-search"></i>
            </div>
            <select class="filter-select" id="filterStatus" onchange="filterUsers()">
                <option value="all">All Users</option>
                <option value="active">Active Buyers</option>
                <option value="inactive">No Orders</option>
            </select>
            <select class="filter-select" id="sortBy" onchange="filterUsers()">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="most-orders">Most Orders</option>
                <option value="highest-spent">Highest Spent</option>
            </select>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <?php if(count($users) > 0): ?>
            <table id="usersTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> User</th>
                        <th><i class="fas fa-envelope"></i> Email</th>
                        <th><i class="fas fa-phone"></i> Phone</th>
                        <th><i class="fas fa-shopping-bag"></i> Orders</th>
                        <th><i class="fas fa-dollar-sign"></i> Total Spent</th>
                        <th><i class="fas fa-calendar"></i> Joined</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr data-user-id="<?= $user['id'] ?>" 
                        data-status="<?= $user['total_orders'] > 0 ? 'active' : 'inactive' ?>"
                        data-orders="<?= $user['total_orders'] ?>"
                        data-spent="<?= $user['total_spent'] ?? 0 ?>"
                        data-joined="<?= strtotime($user['created_at']) ?>">
                        <td><strong>#<?= $user['id'] ?></strong></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['phone'] ? htmlspecialchars($user['phone']) : '<span style="color: #888;">Not set</span>' ?></td>
                        <td><strong><?= $user['total_orders'] ?></strong></td>
                        <td><strong>₱<?= number_format($user['total_spent'] ?? 0, 2) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <span class="status-badge <?= $user['total_orders'] > 0 ? 'status-active' : 'status-inactive' ?>">
                                <?= $user['total_orders'] > 0 ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-view" onclick='viewUser(<?= json_encode($user) ?>)'>
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-delete" onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h2>No Users Found</h2>
                <p>No registered users yet!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-circle"></i> User Details</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="userDetailsContent"></div>
        </div>
    </div>

    <script>
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.animation = 'slideDown 0.5s ease reverse';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);

        // View User Details
        function viewUser(user) {
            const content = `
                <div class="user-detail-row">
                    <span class="user-detail-label">User ID</span>
                    <span class="user-detail-value">#${user.id}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Username</span>
                    <span class="user-detail-value">${user.username}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Email</span>
                    <span class="user-detail-value">${user.email}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Phone</span>
                    <span class="user-detail-value">${user.phone || 'Not set'}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Address</span>
                    <span class="user-detail-value">${user.address || 'Not set'}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Total Orders</span>
                    <span class="user-detail-value">${user.total_orders}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Total Spent</span>
                    <span class="user-detail-value">₱${parseFloat(user.total_spent || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Cart Items</span>
                    <span class="user-detail-value">${user.cart_items}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Wishlist Items</span>
                    <span class="user-detail-value">${user.wishlist_items}</span>
                </div>
                <div class="user-detail-row">
                    <span class="user-detail-label">Joined Date</span>
                    <span class="user-detail-value">${new Date(user.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span>
                </div>
            `;
            
            document.getElementById('userDetailsContent').innerHTML = content;
            document.getElementById('userModal').classList.add('show');
        }

        // Close Modal
        function closeModal() {
            document.getElementById('userModal').classList.remove('show');
        }

        // Delete User
        function deleteUser(userId, username) {
            if (!confirm(`Are you sure you want to delete user "${username}"? This will also delete all their orders, cart items, and wishlist items.`)) {
                return;
            }

            if (!confirm(`This action cannot be undone. Delete "${username}" permanently?`)) {
                return;
            }

            window.location.href = `delete_user.php?id=${userId}`;
        }

        // Filter Users
        function filterUsers() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            const sortBy = document.getElementById('sortBy').value;
            const table = document.getElementById('usersTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));

            // Filter rows
            let visibleRows = rows.filter(row => {
                const username = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const status = row.dataset.status;

                const matchesSearch = username.includes(searchValue) || email.includes(searchValue);
                const matchesStatus = statusFilter === 'all' || status === statusFilter;

                const visible = matchesSearch && matchesStatus;
                row.style.display = visible ? '' : 'none';
                return visible;
            });

            // Sort rows
            visibleRows.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return parseInt(b.dataset.joined) - parseInt(a.dataset.joined);
                    case 'oldest':
                        return parseInt(a.dataset.joined) - parseInt(b.dataset.joined);
                    case 'most-orders':
                        return parseInt(b.dataset.orders) - parseInt(a.dataset.orders);
                    case 'highest-spent':
                        return parseFloat(b.dataset.spent) - parseFloat(a.dataset.spent);
                    default:
                        return 0;
                }
            });

            // Reorder table
            const tbody = table.querySelector('tbody');
            visibleRows.forEach(row => tbody.appendChild(row));
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>