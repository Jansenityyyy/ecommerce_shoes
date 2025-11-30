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

// Get all wishlist items with product details
$items = [];
$brands = ['nike', 'adidas', 'puma'];

foreach ($brands as $brand) {
    $sql = "SELECT w.id as wishlist_id, w.added_at,
                   p.id as product_id, p.name, p.price, p.image, p.description,
                   ? as brand
            FROM wishlist w
            JOIN `$brand` p ON w.product_id = p.id
            WHERE w.user_id = ? AND w.brand = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $brand, $user_id, $brand);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $row['price'] = floatval($row['price']);
        $items[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Check limited products table
$sql = "SELECT w.id as wishlist_id, w.added_at,
               p.id as product_id, p.name, p.price, p.image, p.description,
               p.brand
        FROM wishlist w
        JOIN limited_products p ON w.product_id = p.id
        WHERE w.user_id = ? AND w.brand = 'limited'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $row['price'] = floatval($row['price']);
    $items[] = $row;
}
mysqli_stmt_close($stmt);

// Sort by added date (newest first)
usort($items, function($a, $b) {
    return strtotime($b['added_at']) - strtotime($a['added_at']);
});

echo json_encode(['success' => true, 'data' => $items]);

mysqli_close($conn);
?>