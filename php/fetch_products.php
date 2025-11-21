<?php
include 'connect.php';

// List of allowed brands (tables)
$allowed_brands = ['nike', 'adidas', 'puma'];

// Get brand from query, default to all if not set
$brand = isset($_GET['brand']) ? strtolower($_GET['brand']) : 'all';

// Initialize products array
$products = [];

// Function to fetch products from a table safely
function fetchBrandProducts($conn, $table) {
    // Ensure table name is safe
    $table = mysqli_real_escape_string($conn, $table);
    $sql = "SELECT * FROM `$table`";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            // Format price as PHP string with 2 decimals
            $row['price'] = number_format($row['price'], 2, '.', ',');
            $rows[] = $row;
        }
    }
    return $rows;
}

// Fetch products based on brand
if($brand != 'all' && in_array($brand, $allowed_brands)){
    $products = fetchBrandProducts($conn, $brand);
} else {
    foreach($allowed_brands as $b){
        $products = array_merge($products, fetchBrandProducts($conn, $b));
    }
}

// Return JSON
echo json_encode($products, JSON_UNESCAPED_UNICODE);

// Close connection
mysqli_close($conn);
?>
