<?php
// Admin Delete Product
require_once "../../includes/config.php";

// Check if admin is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid product ID.";
    $_SESSION['message_type'] = "danger";
    header("location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch product data to get the image filename
$sql = "SELECT image FROM products WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $product = mysqli_fetch_assoc($result);
            $image = $product['image'];
            
            // Delete the product from database
            $delete_sql = "DELETE FROM products WHERE id = ?";
            if ($delete_stmt = mysqli_prepare($conn, $delete_sql)) {
                mysqli_stmt_bind_param($delete_stmt, "i", $id);
                  if (mysqli_stmt_execute($delete_stmt)) {
                    // No need to delete image file since we're using URLs now
                    
                    $_SESSION['message'] = "Product deleted successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error deleting product.";
                    $_SESSION['message_type'] = "danger";
                }
                
                mysqli_stmt_close($delete_stmt);
            } else {
                $_SESSION['message'] = "Something went wrong. Please try again later.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Product not found.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again later.";
        $_SESSION['message_type'] = "danger";
    }
    
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = "Something went wrong. Please try again later.";
    $_SESSION['message_type'] = "danger";
}

// Redirect to products page
header("location: index.php");
exit;
?>
