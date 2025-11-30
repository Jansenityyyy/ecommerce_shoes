<?php
session_start();
header('Content-Type: application/json');
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle GET requests (check if item is in wishlist)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'check') {
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
        
        $stmt = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        
        echo json_encode(['success' => true, 'in_wishlist' => $exists]);
        exit();
    }
    
    if (isset($_GET['action']) && $_GET['action'] === 'count') {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        echo json_encode(['success' => true, 'count' => $row['count']]);
        exit();
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        exit();
    }
    
    // Add to wishlist
    if ($action === 'add') {
        // Check if already in wishlist
        $stmt = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
            exit();
        }
        mysqli_stmt_close($stmt);
        
        // Add to wishlist
        $stmt = mysqli_prepare($conn, "INSERT INTO wishlist (user_id, product_id, added_at) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
        } else {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => false, 'message' => 'Error adding to wishlist']);
        }
        exit();
    }
    
    // Remove from wishlist
    if ($action === 'remove') {
        $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
        } else {
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => false, 'message' => 'Error removing from wishlist']);
        }
        exit();
    }
    
    // Toggle wishlist (add if not exists, remove if exists)
    if ($action === 'toggle') {
        // Check if exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        
        if ($exists) {
            // Remove from wishlist
            $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
        } else {
            // Add to wishlist
            $stmt = mysqli_prepare($conn, "INSERT INTO wishlist (user_id, product_id, added_at) VALUES (?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
        }
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
mysqli_close($conn);
?>