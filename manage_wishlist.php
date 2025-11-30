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
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addToWishlist($conn, $user_id);
        break;
    case 'remove':
        removeFromWishlist($conn, $user_id);
        break;
    case 'toggle':
        toggleWishlist($conn, $user_id);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

mysqli_close($conn);

// Add item to wishlist
function addToWishlist($conn, $user_id) {
    $product_id = intval($_POST['product_id']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);

    // Check if item already exists in wishlist
    $check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ? AND brand = ?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
        mysqli_stmt_close($stmt);
        return;
    }
    mysqli_stmt_close($stmt);

    // Insert new item
    $insert = "INSERT INTO wishlist (user_id, product_id, brand, added_at) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding to wishlist']);
    }
    mysqli_stmt_close($stmt);
}

// Remove item from wishlist
function removeFromWishlist($conn, $user_id) {
    $wishlist_id = intval($_POST['wishlist_id']);

    $delete = "DELETE FROM wishlist WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $delete);
    mysqli_stmt_bind_param($stmt, "ii", $wishlist_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing from wishlist']);
    }
    mysqli_stmt_close($stmt);
}

// Toggle wishlist (add if not exists, remove if exists)
function toggleWishlist($conn, $user_id) {
    $product_id = intval($_POST['product_id']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);

    // Check if exists
    $check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ? AND brand = ?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;

    if ($exists) {
        $row = mysqli_fetch_assoc($result);
        $wishlist_id = $row['id'];
        mysqli_stmt_close($stmt);

        // Remove from wishlist
        $delete = "DELETE FROM wishlist WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete);
        mysqli_stmt_bind_param($stmt, "ii", $wishlist_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
    } else {
        mysqli_stmt_close($stmt);

        // Add to wishlist
        $insert = "INSERT INTO wishlist (user_id, product_id, brand, added_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
    }
}
?>