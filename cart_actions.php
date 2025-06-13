<?php
// Cart Actions Handler
require_once "includes/config.php";
require_once "includes/functions.php";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in for cart actions
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    $_SESSION['error_message'] = 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.';
    header("Location: login.php");
    exit();
}

// Get action
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Switch action
switch ($action) {
    case 'add':
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        if ($productId > 0) {
            addToCart($productId, $quantity);
            $_SESSION['message'] = 'Product added to cart successfully.';
        }
        
        // Redirect back to previous page or products page
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: products.php");
        }
        exit;
        
    case 'update':
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        
        if ($productId > 0) {
            updateCartItemQuantity($productId, $quantity);
            $_SESSION['message'] = 'Cart updated successfully.';
        }
        
        header("Location: cart.php");
        exit;
        
    case 'remove':
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if ($productId > 0) {
            removeFromCart($productId);
            $_SESSION['message'] = 'Product removed from cart successfully.';
        }
        
        header("Location: cart.php");
        exit;
        
    case 'clear':
        $_SESSION['cart'] = [];
        $_SESSION['message'] = 'Cart cleared successfully.';
        
        header("Location: cart.php");
        exit;
        
    default:
        // Invalid action, redirect to cart page
        header("Location: cart.php");
        exit;
}
?>
