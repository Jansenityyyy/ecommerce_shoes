<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => true, 'in_wishlist' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$brand = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';

if ($product_id <= 0 || empty($brand)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Check if product is in wishlist
$sql = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ? AND brand = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$in_wishlist = mysqli_num_rows($result) > 0;

echo json_encode(['success' => true, 'in_wishlist' => $in_wishlist]);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>