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
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart($conn, $user_id);
        break;
    case 'remove':
        removeFromCart($conn, $user_id);
        break;
    case 'update':
        updateQuantity($conn, $user_id);
        break;
    case 'get':
        getCart($conn, $user_id);
        break;
    case 'count':
        getCartCount($conn, $user_id);
        break;
    case 'clear':
        clearCart($conn, $user_id);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

// Add item to cart
function addToCart($conn, $user_id) {
    $product_id = intval($_POST['product_id']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Check if item already exists in cart
    $check = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND brand = ?";
    $stmt = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $brand);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Update quantity if exists
        $row = mysqli_fetch_assoc($result);
        $new_qty = $row['quantity'] + $quantity;
        $update = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "ii", $new_qty, $row['id']);
        mysqli_stmt_execute($stmt);
    } else {
        // Insert new item
        $insert = "INSERT INTO cart (user_id, product_id, brand, quantity) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, "iisi", $user_id, $product_id, $brand, $quantity);
        mysqli_stmt_execute($stmt);
    }

    echo json_encode(['success' => true, 'message' => 'Added to cart']);
}

// Remove item from cart
function removeFromCart($conn, $user_id) {
    $cart_id = intval($_POST['cart_id']);

    $delete = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $delete);
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true, 'message' => 'Removed from cart']);
}

// Update quantity
function updateQuantity($conn, $user_id) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        // Remove if quantity is 0 or less
        $delete = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete);
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
    } else {
        $update = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
    }

    echo json_encode(['success' => true, 'message' => 'Cart updated']);
}

// Get all cart items with product details
function getCart($conn, $user_id) {
    $items = [];
    $brands = ['nike', 'adidas', 'puma'];

    foreach ($brands as $brand) {
        $sql = "SELECT c.id as cart_id, c.quantity, c.brand, 
                       p.id as product_id, p.name, p.price, p.image, p.description
                FROM cart c
                JOIN `$brand` p ON c.product_id = p.id
                WHERE c.user_id = ? AND c.brand = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $brand);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $row['price'] = floatval($row['price']);
            $items[] = $row;
        }
    }

    echo json_encode($items);
}

// Get cart item count
function getCartCount($conn, $user_id) {
    $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    echo json_encode(['count' => intval($row['count'] ?? 0)]);
}

// Clear entire cart
function clearCart($conn, $user_id) {
    $delete = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true, 'message' => 'Cart cleared']);
}

mysqli_close($conn);
?>