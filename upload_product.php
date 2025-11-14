<?php
include '../php/connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $brand = $_POST['brand'];
    $name = $_POST['name'];

    // Remove ₱ and commas for proper float
    $price = floatval(str_replace(['₱',',',' '], '', $_POST['price']));
    $description = $_POST['description'];

    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $imgName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        // Create brand folder if it doesn't exist
        $targetDir = "../src/img/$brand/";
        if(!is_dir($targetDir)){
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $targetDir . basename($imgName);

        if(move_uploaded_file($tmpName, $targetFile)){
            $imgPath = "$brand/$imgName"; // relative path

            // Insert into brand table
            $sql = "INSERT INTO $brand (name, price, image, description) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "siss", $name, $price, $imgPath, $description);

            if(mysqli_stmt_execute($stmt)){
                echo "Product added successfully!";
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
