<?php
// Disable error display, enable logging only
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any stray output
ob_start();

session_start();

// Set JSON header immediately
header('Content-Type: application/json');

// Include database connection
if (!file_exists('connect.php')) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found']);
    exit;
}

include 'connect.php';

// Check database connection
if (!isset($conn) || !$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Check request method and action
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['action']) || $_POST['action'] !== 'place_order') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate required fields
$required_fields = ['full_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
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

// Get and validate cart items
if (empty($_POST['items'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No items provided']);
    exit;
}

$items = json_decode($_POST['items'], true);

if (!is_array($items) || empty($items)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    if (!isset($item['price']) || !isset($item['quantity'])) {
        ob_end_clean();
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
        full_name, email, phone, address, city, province, postal_code, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare order statement: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param(
        $stmt, "isddssssssss",
        $user_id, $order_number, $total_amount, $shipping_fee, $payment_method,
        $full_name, $email, $phone, $address, $city, $province, $postal_code, $notes
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to insert order: ' . mysqli_stmt_error($stmt));
    }
    
    $order_id = mysqli_insert_id($conn);

    // Insert order items
    $sql_item = "INSERT INTO order_items (
        order_id, product_id, brand, product_name, product_image, price, quantity, subtotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_item = mysqli_prepare($conn, $sql_item);
    
    if (!$stmt_item) {
        throw new Exception('Failed to prepare order items statement: ' . mysqli_error($conn));
    }

    foreach ($items as $item) {
        $product_id = intval($item['product_id']);
        $brand = isset($item['brand']) ? $item['brand'] : '';
        $name = isset($item['name']) ? $item['name'] : 'Unknown Product';
        $image = isset($item['image']) ? $item['image'] : '';
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $item_subtotal = $price * $quantity;
        
        mysqli_stmt_bind_param(
            $stmt_item, "iisssdid",
            $order_id,
            $product_id,
            $brand,
            $name,
            $image,
            $price,
            $quantity,
            $item_subtotal
        );
        
        if (!mysqli_stmt_execute($stmt_item)) {
            throw new Exception('Failed to insert order item: ' . mysqli_stmt_error($stmt_item));
        }
    }

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
    
    // Close statements
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_item);

    // Clear output buffer and send success response
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'order_number' => $order_number,
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    // Clear output buffer and send error response
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error processing order: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
exit;
?>