<?php
// Helper functions for the shop

// Function to get all products
function getAllProducts($conn) {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Function to get a single product by ID
function getProductById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Function to search products
function searchProducts($conn, $query) {
    $query = mysqli_real_escape_string($conn, $query);
    $sql = "SELECT * FROM products WHERE 
            name LIKE '%$query%' OR 
            description LIKE '%$query%' OR 
            category LIKE '%$query%'";
    
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Function to add a product to cart
function addToCart($productId, $quantity = 1) {
    // Get product from database
    global $conn;
    $product = getProductById($conn, $productId);
    
    if (!$product) {
        return false;
    }
    
    // Check if product is already in cart
    if (isset($_SESSION['cart'][$productId])) {
        // Increase quantity
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        // Add product to cart
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }
    
    return true;
}

// Function to update cart item quantity
function updateCartItemQuantity($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
        return true;
    }
    return false;
}

// Function to remove item from cart
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }
    return false;
}

// Function to calculate cart total
function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

// Function to create a new order
function createOrder($conn, $customerData) {
    $name = mysqli_real_escape_string($conn, $customerData['name']);
    $email = mysqli_real_escape_string($conn, $customerData['email']);
    $phone = mysqli_real_escape_string($conn, $customerData['phone']);
    $address = mysqli_real_escape_string($conn, $customerData['address']);
    $total = getCartTotal();
    
    // Insert order into database
    $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount) 
            VALUES ('$name', '$email', '$phone', '$address', $total)";
    
    if (mysqli_query($conn, $sql)) {
        $orderId = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($_SESSION['cart'] as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES ($orderId, $productId, $quantity, $price)";
            mysqli_query($conn, $sql);
            
            // Update product stock
            $sql = "UPDATE products SET stock = stock - $quantity WHERE id = $productId";
            mysqli_query($conn, $sql);
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        return $orderId;
    }
    
    return false;
}

// Function to format price
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' đ';
}
?>
