<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? '';

switch ($type) {
    case 'orders':
        getOrdersCount($conn, $user_id);
        break;
    case 'cart':
        getCartCount($conn, $user_id);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
}

mysqli_close($conn);

// Get total orders count
function getOrdersCount($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    echo json_encode([
        'success' => true,
        'count' => intval($row['count'])
    ]);
}

// Get cart items count
function getCartCount($conn, $user_id) {
    $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    echo json_encode([
        'success' => true,
        'count' => intval($row['count'] ?? 0)
    ]);
}
?>