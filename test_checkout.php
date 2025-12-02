<?php
session_start();
include 'php/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

$user_id = $_SESSION['user_id'];

// Test database connection
echo "<h2>Database Connection Test</h2>";
if ($conn) {
    echo "✅ Connected to database<br>";
} else {
    echo "❌ Database connection failed<br>";
    die();
}

// Check if orders table exists
echo "<h2>Tables Check</h2>";
$tables = ['orders', 'order_items', 'users', 'cart'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Table '$table' exists<br>";
    } else {
        echo "❌ Table '$table' does NOT exist<br>";
    }
}

// Check user session
echo "<h2>Session Check</h2>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Username: " . $_SESSION['username'] . "<br>";

// Get cart items
echo "<h2>Cart Items Check</h2>";
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "✅ Found " . mysqli_num_rows($result) . " items in cart<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- Product ID: {$row['product_id']}, Brand: {$row['brand']}, Quantity: {$row['quantity']}<br>";
    }
} else {
    echo "❌ No items in cart<br>";
}

// Test order insertion
echo "<h2>Test Order Creation</h2>";
$test_order_number = 'TEST-' . time();
$test_sql = "INSERT INTO orders (
    user_id, order_number, total_amount, shipping_fee, payment_method,
    full_name, email, phone, address, city, province, postal_code,
    order_status, payment_status
) VALUES (?, ?, 1000.00, 150.00, 'cod', 'Test User', 'test@test.com', '123456789', 
          'Test Address', 'Test City', 'Test Province', '1234', 'pending', 'pending')";

$test_stmt = mysqli_prepare($conn, $test_sql);
if ($test_stmt) {
    mysqli_stmt_bind_param($test_stmt, "is", $user_id, $test_order_number);
    if (mysqli_stmt_execute($test_stmt)) {
        $test_order_id = mysqli_insert_id($conn);
        echo "✅ Test order created successfully! Order ID: $test_order_id<br>";
        
        // Delete test order
        mysqli_query($conn, "DELETE FROM orders WHERE id = $test_order_id");
        echo "✅ Test order deleted<br>";
    } else {
        echo "❌ Failed to create test order: " . mysqli_stmt_error($test_stmt) . "<br>";
    }
    mysqli_stmt_close($test_stmt);
} else {
    echo "❌ Failed to prepare test statement: " . mysqli_error($conn) . "<br>";
}

// Test PHP error logging
echo "<h2>PHP Configuration</h2>";
echo "Error Reporting: " . error_reporting() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Log Errors: " . ini_get('log_errors') . "<br>";

echo "<h2>All Tests Complete</h2>";
echo "<a href='checkout.php'>Go to Checkout</a>";

mysqli_close($conn);
?>