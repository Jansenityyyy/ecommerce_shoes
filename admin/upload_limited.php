<?php
include '../php/connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $brand = $_POST['brand'];
    $name = $_POST['name'];
    $price = floatval(str_replace(['₱',',',' '], '', $_POST['price']));
    $description = $_POST['description'];

    // Ensure dates are in YYYY-MM-DD format
    $start_date = date('Y-m-d', strtotime($_POST['start_date']));
    $end_date = date('Y-m-d', strtotime($_POST['end_date']));

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $imgName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        $targetDir = "../src/img/$brand/";
        if(!is_dir($targetDir)){
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . basename($imgName);

        if(move_uploaded_file($tmpName, $targetFile)){
            $imgPath = "$brand/$imgName";

            $sql = "INSERT INTO limited_products (brand, name, price, image, description, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdssss", $brand, $name, $price, $imgPath, $description, $start_date, $end_date);

            if(mysqli_stmt_execute($stmt)){
                header("Location: index.php?success=1");
                exit;
            } else {
                echo "Database error: " . mysqli_error($conn);
            }
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "No image selected or upload error.";
    }

    mysqli_close($conn);
} else {
    echo "Invalid request.";
}
?>
