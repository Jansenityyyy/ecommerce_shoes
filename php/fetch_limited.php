<?php
include 'connect.php';

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

// Fetch active limited product
$sql = "SELECT *, DATE_FORMAT(end_date, '%Y-%m-%d') AS end_date_formatted FROM limited_products WHERE start_date <= ? AND end_date >= ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $today, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($result && mysqli_num_rows($result) > 0){
    $product = mysqli_fetch_assoc($result);
    $product['price'] = floatval($product['price']); 
    $product['end_date'] = $product['end_date_formatted']; // JS-friendly
    echo json_encode($product, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(null);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
