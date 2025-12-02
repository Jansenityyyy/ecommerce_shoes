<?php
// Disable all output buffering and error display
error_reporting(0);
ini_set('display_errors', 0);

// Start session
session_start();

// Set JSON header at the very beginning
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check action
if (!isset($_POST['action']) || $_POST['action'] !== 'place_order') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate required fields
$required_fields = ['full_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

// Get and sanitize form data
$full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
$address = mysqli_real_escape_string($conn, trim($_POST['address']));
$city = mysqli_real_escape_string($conn, trim($_POST['city']));
$province = mysqli_real_escape_string($conn, trim($_POST['province']));
$postal_code = mysqli_real_escape_string($conn, trim($_POST['postal_code']));
$notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, trim($_POST['notes'])) : '';
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

// Get cart items
if (empty($_POST['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items in order']);
    exit;
}

$items = json_decode($_POST['items'], true);

if (!is_array($items) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    if (!isset($item['price']) || !isset($item['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid item data']);
        exit;
    }
    $subtotal += floatval($item['price']) * intval($item['quantity']);
}

$shipping_fee = 150.00;
$total_amount = $subtotal + $shipping_fee;

// Generate order number
$order_number = 'SS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert order
    $sql = "INSERT INTO orders (
        user_id, order_number, total_amount, shipping_fee, payment_method,
        full_name, email, phone, address, city, province, postal_code, notes,
        order_status, payment_status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    mysqli_stmt_bind_param(
        $stmt, "isddssssssss",
        $user_id, $order_number, $total_amount, $shipping_fee, $payment_method,
        $full_name, $email, $phone, $address, $city, $province, $postal_code, $notes
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to create order');
    }
    
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Insert order items
    $sql_item = "INSERT INTO order_items (
        order_id, product_id, brand, product_name, product_image, price, quantity, subtotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_item = mysqli_prepare($conn, $sql_item);
    
    if (!$stmt_item) {
        throw new Exception('Failed to prepare item statement');
    }

    foreach ($items as $item) {
        $product_id = intval($item['product_id']);
        $brand = isset($item['brand']) ? $item['brand'] : '';
        $name = isset($item['name']) ? $item['name'] : 'Unknown';
        $image = isset($item['image']) ? $item['image'] : '';
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $item_subtotal = $price * $quantity;
        
        mysqli_stmt_bind_param(
            $stmt_item, "iisssdid",
            $order_id, $product_id, $brand, $name, $image, $price, $quantity, $item_subtotal
        );
        
        if (!mysqli_stmt_execute($stmt_item)) {
            throw new Exception('Failed to add item to order');
        }
    }
    
    mysqli_stmt_close($stmt_item);

    // Clear user's cart
    $sql_clear = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear = mysqli_prepare($conn, $sql_clear);
    
    if ($stmt_clear) {
        mysqli_stmt_bind_param($stmt_clear, "i", $user_id);
        mysqli_stmt_execute($stmt_clear);
        mysqli_stmt_close($stmt_clear);
    }

    // Commit transaction
    mysqli_commit($conn);

    // Success response
    echo json_encode([
        'success' => true,
        'order_number' => $order_number,
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => 'Error processing order: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
exit;
?>