<?php
include '../php/connect.php';

if(isset($_GET['id'], $_GET['brand'])){
    $id = intval($_GET['id']);
    $brand = $_GET['brand'];

    if($brand == 'limited'){
        $table = 'limited_products';
    } else {
        $table = in_array($brand, ['nike','adidas','puma']) ? $brand : '';
    }

    if($table){
        // Delete the product
        $sql = "DELETE FROM `$table` WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
header("Location: admin_products.php");
exit;
?>
