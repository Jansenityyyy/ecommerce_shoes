<?php
include 'connect.php';

$allowed_brands = ['nike', 'adidas', 'puma'];

$brand = isset($_GET['brand']) ? strtolower($_GET['brand']) : 'all';

$products = [];

function fetchBrandProducts($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table);
    $sql = "SELECT * FROM `$table`";
    $result = mysqli_query($conn, $sql);
    $rows = [];

    if($result){
        while($row = mysqli_fetch_assoc($result)){
            // Keep raw number (avoid formatting here)
            $row['price'] = floatval($row['price']);
            $rows[] = $row;
        }
    }
    return $rows;
}

if($brand != 'all' && in_array($brand, $allowed_brands)){
    $products = fetchBrandProducts($conn, $brand);
} else {
    foreach($allowed_brands as $b){
        $products = array_merge($products, fetchBrandProducts($conn, $b));
    }
}

echo json_encode($products, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>
