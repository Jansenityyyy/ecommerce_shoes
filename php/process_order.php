<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_POST['action'] !== 'place_order') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get form data
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$province = mysqli_real_escape_string($conn, $_POST['province']);
$postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

// Get cart items
$items = json_decode($_POST['items'], true);

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
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
    mysqli_stmt_bind_param(
        $stmt, "isddssssssss",
        $user_id, $order_number, $total_amount, $shipping_fee, $payment_method,
        $full_name, $email, $phone, $address, $city, $province, $postal_code, $notes
    );
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    // Insert order items
    $sql_item = "INSERT INTO order_items (
        order_id, product_id, brand, product_name, product_image, price, quantity, subtotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_item = mysqli_prepare($conn, $sql_item);

    foreach ($items as $item) {
        $item_subtotal = $item['price'] * $item['quantity'];
        mysqli_stmt_bind_param(
            $stmt_item, "iisssdid",
            $order_id,
            $item['product_id'],
            $item['brand'],
            $item['name'],
            $item['image'],
            $item['price'],
            $item['quantity'],
            $item_subtotal
        );
        mysqli_stmt_execute($stmt_item);
    }

    // Clear user's cart
    $sql_clear = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear = mysqli_prepare($conn, $sql_clear);
    mysqli_stmt_bind_param($stmt_clear, "i", $user_id);
    mysqli_stmt_execute($stmt_clear);

    // Commit transaction
    mysqli_commit($conn);

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
?>