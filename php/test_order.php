<?php
// Simple test file to debug order processing
session_start();
header('Content-Type: application/json');

// Test 1: Check session
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'test' => 'failed',
        'error' => 'Not logged in',
        'session' => $_SESSION
    ]);
    exit;
}

// Test 2: Check database connection
include 'connect.php';
if (!$conn) {
    echo json_encode([
        'test' => 'failed',
        'error' => 'Database connection failed',
        'mysqli_error' => mysqli_connect_error()
    ]);
    exit;
}

// Test 3: Check if tables exist
$tables_check = [];
$tables = ['orders', 'order_items', 'cart', 'users'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    $tables_check[$table] = mysqli_num_rows($result) > 0;
}

// Test 4: Check POST data
$post_data = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'action' => $_POST['action'] ?? 'not set',
    'items_exists' => isset($_POST['items']),
    'form_fields' => [
        'full_name' => isset($_POST['full_name']),
        'email' => isset($_POST['email']),
        'phone' => isset($_POST['phone']),
        'payment_method' => isset($_POST['payment_method'])
    ]
];

echo json_encode([
    'test' => 'success',
    'user_id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'] ?? 'not set',
    'tables_exist' => $tables_check,
    'post_data' => $post_data,
    'php_version' => phpversion(),
    'server_time' => date('Y-m-d H:i:s')
]);

mysqli_close($conn);
?>