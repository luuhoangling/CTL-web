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
    $sql = "SELECT * FROM products WHERE LOWER(name) COLLATE utf8mb4_bin LIKE LOWER('%$query%')";
    
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
    // Sanitize input data
    $name = $customerData['name'];
    $email = $customerData['email'];
    $phone = $customerData['phone'];
    $address = $customerData['address'];
    $payment_method = $customerData['payment_method'];
    $note = $customerData['note'];
    $total = getCartTotal();
    
    // Create transaction ID for demo only (in real implementation this would come from payment gateway)
    $transaction_id = $payment_method != 'COD' ? 'DEMO_' . strtoupper(substr(md5(time()), 0, 10)) : null;        // Insert order into database
    $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, 
                               total_amount, payment_method, payment_status, transaction_id, note) 
            VALUES ('$name', '$email', '$phone', '$address', $total, 
                   '$payment_method', 'pending', " . 
                   ($transaction_id ? "'$transaction_id'" : "NULL") . ", " . 
                   ($note ? "'$note'" : "NULL") . ")";
                   
    // Debug order creation
    // error_log("Creating order: " . $sql);
    
    if (mysqli_query($conn, $sql)) {
        $orderId = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($_SESSION['cart'] as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $variantId = isset($item['variant_id']) ? $item['variant_id'] : 'NULL';
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, variant_id) 
                    VALUES ($orderId, $productId, $quantity, $price, $variantId)";
            mysqli_query($conn, $sql);
            
            // Update product stock
            if ($variantId != 'NULL') {
                // Update variant stock if variant exists
                $sql = "UPDATE product_variants SET stock = stock - $quantity WHERE id = $variantId";
            } else {
                // Otherwise update main product stock
                $sql = "UPDATE products SET stock = stock - $quantity WHERE id = $productId";
            }
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

// User authentication functions
function registerUser($conn, $username, $email, $password, $full_name) {
    // Check if username or email already exists
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);
    
    $checkSql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        return false; // User already exists
    }
    
    // Store password without hashing
    $password = mysqli_real_escape_string($conn, $password);
    $full_name = mysqli_real_escape_string($conn, $full_name);
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password, full_name, isAdmin) VALUES ('$username', '$email', '$password', '$full_name', 0)";
    
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    }
    
    return false;
}

function loginUser($conn, $username, $password) {
    // Input validation
    if (empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Tên đăng nhập và mật khẩu không được để trống.'];
    }
    
    // Sanitize input
    $username = trim($username);
    $password = trim($password);
      // Additional validation
    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['success' => false, 'error' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (3-50 ký tự)'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Mật khẩu phải có ít nhất 6 ký tự.'];
    }
    
    // Check for basic rate limiting (simple implementation)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $current_time = time();
    
    // Initialize login attempts if not exists
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    // Clean old attempts (older than 15 minutes)
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($attempt) use ($current_time) {
        return ($current_time - $attempt) < 900; // 15 minutes
    });
    
    // Check if too many attempts
    if (count($_SESSION['login_attempts']) >= 5) {
        return ['success' => false, 'error' => 'Quá nhiều lần đăng nhập sai. Vui lòng thử lại sau 15 phút.'];
    }
    
    // Use prepared statement to prevent SQL injection
    $sql = "SELECT id, username, email, password, full_name, isAdmin FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return ['success' => false, 'error' => 'Lỗi hệ thống. Vui lòng thử lại sau.'];
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
      if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Simple password comparison (plain text)
        if ($password === $user['password']) {
            // Clear login attempts on successful login
            $_SESSION['login_attempts'] = [];
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $_SESSION['full_name'] = htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8');
            $_SESSION['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
            $_SESSION['isAdmin'] = (int)$user['isAdmin'];
            $_SESSION['login_time'] = time();
            
            mysqli_stmt_close($stmt);
            return ['success' => true, 'isAdmin' => (int)$user['isAdmin']];
        }
    }
    
    // Record failed login attempt
    $_SESSION['login_attempts'][] = $current_time;
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'error' => 'Tên đăng nhập hoặc mật khẩu không đúng.'];
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
    
    // Nếu là admin (isAdmin = 1) thì set admin session và chuyển đến admin
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $_SESSION['user_id'];
        $_SESSION['admin_username'] = $_SESSION['username'];
        $_SESSION['admin_full_name'] = $_SESSION['full_name'];
        
        header("Location: admin/index.php");
        exit();
    }
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

function getCurrentUser($conn) {
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        $sql = "SELECT * FROM users WHERE id = $userId";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    
    return null;
}

// Admin authentication functions
function loginAdmin($conn, $username, $password) {
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    
    // Check admin credentials (isAdmin = 1)
    $sql = "SELECT id, username, email, password, full_name, isAdmin FROM users WHERE username = '$username' AND password = '$password' AND isAdmin = 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // Set admin session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_full_name'] = $admin['full_name'];
        
        return true;
    }
    
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Security helper functions
function generateCSRFToken() {
    // Ensure session is active
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    // Generate new token if not exists or expired
    if (empty($_SESSION['csrf_token']) || 
        (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600)) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    // Ensure session is active
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    // Check if token exists and is not expired (1 hour)
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Check if token is expired (1 hour)
    if ((time() - $_SESSION['csrf_token_time']) > 3600) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
}

function isValidPassword($password) {
    return strlen($password) >= 6 && strlen($password) <= 255;
}

function logSecurityEvent($event, $details = '') {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // In production, you might want to log this to a file or database
    error_log("Security Event: $event | IP: $ip | Details: $details | Time: $timestamp | User-Agent: $user_agent");
}

// Function to get all categories
function getAllCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("MySQL Error in getAllCategories: " . mysqli_error($conn));
        return [];
    }
    
    $categories = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Function to get attributes by category
function getAttributesByCategory($conn, $categoryId) {
    $categoryId = intval($categoryId);
    $sql = "SELECT a.*, GROUP_CONCAT(av.id, ':', av.value SEPARATOR '|') as `values`
            FROM attributes a 
            LEFT JOIN attribute_values av ON a.id = av.attribute_id 
            WHERE a.category_id = $categoryId 
            GROUP BY a.id 
            ORDER BY a.name";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("MySQL Error in getAttributesByCategory: " . mysqli_error($conn));
        return [];
    }
    
    $attributes = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $values = [];
            if ($row['values']) {
                $valuesList = explode('|', $row['values']);
                foreach ($valuesList as $value) {
                    $parts = explode(':', $value, 2);
                    if (count($parts) == 2) {
                        $values[] = ['id' => $parts[0], 'value' => $parts[1]];
                    }
                }
            }
            $row['values'] = $values;
            $attributes[] = $row;
        }
    }
    
    return $attributes;
}

// Function to get products with filters
function getProductsWithFilters($conn, $filters = array()) {
    $whereClause = "1=1";
    $joins = "";
    
    // Search query filter
    if (!empty($filters['search'])) {
        $search = mysqli_real_escape_string($conn, $filters['search']);
        $whereClause .= " AND (LOWER(p.name) COLLATE utf8mb4_bin LIKE LOWER('%$search%') 
                         OR LOWER(p.description) COLLATE utf8mb4_bin LIKE LOWER('%$search%'))";
    }
    
    // Category filter
    if (!empty($filters['category_id'])) {
        $categoryId = intval($filters['category_id']);
        $whereClause .= " AND p.category_id = $categoryId";
    }
    
    // Price range filter
    if (!empty($filters['price_min'])) {
        $priceMin = floatval($filters['price_min']);
        $whereClause .= " AND p.price >= $priceMin";
    }
    
    if (!empty($filters['price_max'])) {
        $priceMax = floatval($filters['price_max']);
        $whereClause .= " AND p.price <= $priceMax";
    }
    
    // Attribute filters
    if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
        $attributeConditions = [];
        $joinCounter = 0;
        
        foreach ($filters['attributes'] as $attributeId => $valueIds) {
            if (!empty($valueIds) && is_array($valueIds)) {
                $joinCounter++;
                $joins .= " INNER JOIN product_variants pv$joinCounter ON p.id = pv$joinCounter.product_id";
                $joins .= " INNER JOIN variant_attribute_values vav$joinCounter ON pv$joinCounter.id = vav$joinCounter.variant_id";
                
                $valueIdsStr = implode(',', array_map('intval', $valueIds));
                $attributeConditions[] = "vav$joinCounter.attribute_value_id IN ($valueIdsStr)";
            }
        }
        
        if (!empty($attributeConditions)) {
            $whereClause .= " AND " . implode(' AND ', $attributeConditions);
        }
    }
    
    // Sort options
    $orderBy = "p.id DESC";
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price_asc':
                $orderBy = "p.price ASC";
                break;
            case 'price_desc':
                $orderBy = "p.price DESC";
                break;
            case 'name_asc':
                $orderBy = "p.name ASC";
                break;
            case 'newest':
                $orderBy = "p.created_at DESC";
                break;
            default:
                $orderBy = "p.id DESC";
        }
    }
    
    $sql = "SELECT DISTINCT p.* FROM products p $joins WHERE $whereClause ORDER BY $orderBy";
    
    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql);
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn));
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Function to get price range for products
function getPriceRange($conn, $categoryId = null) {
    $whereClause = "1=1";
    if ($categoryId) {
        $categoryId = intval($categoryId);
        $whereClause .= " AND category_id = $categoryId";
    }
    
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE $whereClause";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("MySQL Error in getPriceRange: " . mysqli_error($conn));
        return ['min_price' => 0, 'max_price' => 0];
    }
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'min_price' => $row['min_price'] ?? 0,
            'max_price' => $row['max_price'] ?? 0
        ];
    }
    
    return ['min_price' => 0, 'max_price' => 0];
}

// Function to count products with filters
function countProductsWithFilters($conn, $filters = array()) {
    $whereClause = "1=1";
    $joins = "";
    
    // Search query filter
    if (!empty($filters['search'])) {
        $search = mysqli_real_escape_string($conn, $filters['search']);
        $whereClause .= " AND (LOWER(p.name) COLLATE utf8mb4_bin LIKE LOWER('%$search%') 
                         OR LOWER(p.description) COLLATE utf8mb4_bin LIKE LOWER('%$search%'))";
    }
    
    // Category filter
    if (!empty($filters['category_id'])) {
        $categoryId = intval($filters['category_id']);
        $whereClause .= " AND p.category_id = $categoryId";
    }
    
    // Price range filter
    if (!empty($filters['price_min'])) {
        $priceMin = floatval($filters['price_min']);
        $whereClause .= " AND p.price >= $priceMin";
    }
    
    if (!empty($filters['price_max'])) {
        $priceMax = floatval($filters['price_max']);
        $whereClause .= " AND p.price <= $priceMax";
    }
    
    // Attribute filters
    if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
        $attributeConditions = [];
        $joinCounter = 0;
        
        foreach ($filters['attributes'] as $attributeId => $valueIds) {
            if (!empty($valueIds) && is_array($valueIds)) {
                $joinCounter++;
                $joins .= " INNER JOIN product_variants pv$joinCounter ON p.id = pv$joinCounter.product_id";
                $joins .= " INNER JOIN variant_attribute_values vav$joinCounter ON pv$joinCounter.id = vav$joinCounter.variant_id";
                
                $valueIdsStr = implode(',', array_map('intval', $valueIds));
                $attributeConditions[] = "vav$joinCounter.attribute_value_id IN ($valueIdsStr)";
            }
        }
        
        if (!empty($attributeConditions)) {
            $whereClause .= " AND " . implode(' AND ', $attributeConditions);
        }
    }
    
    $sql = "SELECT COUNT(DISTINCT p.id) as total FROM products p $joins WHERE $whereClause";
    
    // Debug: Log the SQL query
    error_log("Count SQL Query: " . $sql);
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("MySQL Count Error: " . mysqli_error($conn));
        throw new Exception("Database count query failed: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return intval($row['total']);
    }
    
    return 0;
}
?>
