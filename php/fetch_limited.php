<?php
include 'connect.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Get today's date
$today = date('Y-m-d');

// Fetch the currently active limited product
$sql = "SELECT * FROM limited_products WHERE start_date <= ? AND end_date >= ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $today, $today);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Return product as JSON
if($result && mysqli_num_rows($result) > 0){
    $product = mysqli_fetch_assoc($result);
    
    // Optional: make price formatted here if you want PHP-side formatting
    $product['price'] = number_format($product['price'], 2, '.', ','); 

    echo json_encode($product, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(null); // no active limited product today
}

// Close connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
