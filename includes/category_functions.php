<?php
// Category and filtering functions for multi-product e-commerce

// Ensure base functions are loaded
require_once __DIR__ . '/functions.php';

// Get products by category
function getProductsByCategory($conn, $categoryId) {
    $categoryId = mysqli_real_escape_string($conn, $categoryId);
    error_log("In getProductsByCategory for categoryId: " . $categoryId);
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = $categoryId 
            ORDER BY p.created_at DESC";
    
    error_log("SQL query: " . $sql);
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        error_log("SQL Error: " . mysqli_error($conn));
        return [];
    }
    
    $products = [];
    $count = mysqli_num_rows($result);
    error_log("Found $count products for category $categoryId");
    
    if ($count > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get attributes for a category
function getCategoryAttributes($conn, $categoryId) {
    $categoryId = mysqli_real_escape_string($conn, $categoryId);
    
    // Log SQL và thông tin vào error_log
    error_log("Getting attributes for category ID: " . $categoryId);
    
    $sql = "SELECT a.id, a.name, a.category_id FROM attributes a WHERE a.category_id = $categoryId ORDER BY a.name";
    $result = mysqli_query($conn, $sql);
    
    $attributes = [];
    if (mysqli_num_rows($result) > 0) {
        while ($attribute = mysqli_fetch_assoc($result)) {
            // Get values for this attribute
            $attributeId = $attribute['id'];
            $valuesSql = "SELECT av.value FROM attribute_values av WHERE av.attribute_id = $attributeId";
            $valuesResult = mysqli_query($conn, $valuesSql);
            
            $values = [];
            if (mysqli_num_rows($valuesResult) > 0) {
                while ($valueRow = mysqli_fetch_assoc($valuesResult)) {
                    $values[] = $valueRow['value'];
                }
            }
            
            $attribute['values'] = $values;
            $attributes[] = $attribute;
        }
    }
    
    error_log("Found attributes: " . print_r($attributes, true));
    return $attributes;
}

// Get product variants
function getProductVariants($conn, $productId) {
    $productId = mysqli_real_escape_string($conn, $productId);
    $sql = "SELECT pv.*, 
            GROUP_CONCAT(CONCAT(a.name, ':', av.value) SEPARATOR ', ') as variant_attributes
            FROM product_variants pv
            LEFT JOIN variant_attribute_values vav ON pv.id = vav.variant_id
            LEFT JOIN attribute_values av ON vav.attribute_value_id = av.id
            LEFT JOIN attributes a ON av.attribute_id = a.id
            WHERE pv.product_id = $productId
            GROUP BY pv.id
            ORDER BY pv.price";
    
    $result = mysqli_query($conn, $sql);
    
    $variants = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $variants[] = $row;
        }
    }
    
    return $variants;
}

// Filter products by multiple criteria
function filterProducts($conn, $filters = []) {
    $sql = "SELECT DISTINCT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            LEFT JOIN variant_attribute_values vav ON pv.id = vav.variant_id
            LEFT JOIN attribute_values av ON vav.attribute_value_id = av.id
            LEFT JOIN attributes a ON av.attribute_id = a.id
            WHERE 1=1";
    
    // Category filter
    if (!empty($filters['category_id'])) {
        $categoryId = (int)$filters['category_id']; // Đảm bảo là số nguyên
        $sql .= " AND p.category_id = $categoryId"; // Bỏ dấu nháy đơn
    }
      // Price range filter
    if (!empty($filters['min_price'])) {
        $minPrice = validatePrice($filters['min_price']);
        if ($minPrice !== '') {
            $minPrice = mysqli_real_escape_string($conn, $minPrice);
            $sql .= " AND p.price >= '$minPrice'";
        }
    }
    
    if (!empty($filters['max_price'])) {
        $maxPrice = validatePrice($filters['max_price']);
        if ($maxPrice !== '') {
            $maxPrice = mysqli_real_escape_string($conn, $maxPrice);
            $sql .= " AND p.price <= '$maxPrice'";
        }
    }
    
    // Attribute filters
    if (!empty($filters['attributes'])) {
        foreach ($filters['attributes'] as $attributeId => $values) {
            if (!empty($values)) {
                $attributeId = mysqli_real_escape_string($conn, $attributeId);
                $valuesList = "'" . implode("','", array_map(function($v) use ($conn) {
                    return mysqli_real_escape_string($conn, $v);
                }, $values)) . "'";
                
                $sql .= " AND EXISTS (
                    SELECT 1 FROM product_variants pv2
                    JOIN variant_attribute_values vav2 ON pv2.id = vav2.variant_id
                    JOIN attribute_values av2 ON vav2.attribute_value_id = av2.id
                    WHERE pv2.product_id = p.id 
                    AND av2.attribute_id = $attributeId 
                    AND av2.value IN ($valuesList)
                )";
            }
        }
    }
    
    // Search query
    if (!empty($filters['search'])) {
        $search = mysqli_real_escape_string($conn, $filters['search']);
        $sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
    }
    
    // Sorting    // Sorting
    $orderBy = " ORDER BY ";
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price_asc':
                $orderBy .= "p.price ASC";
                break;
            case 'price_desc':
                $orderBy .= "p.price DESC";
                break;
            case 'name':
                $orderBy .= "p.name ASC";
                break;
            case 'newest':
            default:
                $orderBy .= "p.created_at DESC";
                break;
        }
    } else {
        $orderBy .= "p.created_at DESC";
    }
    $sql .= $orderBy;
    
    // Add limit if specified
    if (!empty($filters['limit'])) {
        $limit = (int)$filters['limit'];
        $sql .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get price range for category
function getCategoryPriceRange($conn, $categoryId = null) {
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE 1=1";
    
    if ($categoryId) {
        $categoryId = mysqli_real_escape_string($conn, $categoryId);
        $sql .= " AND category_id = $categoryId";
    }
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return ['min_price' => 0, 'max_price' => 0];
}

// Validate and sanitize price input
function validatePrice($price) {
    if (empty($price)) {
        return '';
    }
    
    // Remove non-numeric characters except dots and commas
    $price = preg_replace('/[^0-9.,]/', '', $price);
    
    // Convert comma to dot for decimal
    $price = str_replace(',', '.', $price);
    
    // Validate as numeric
    if (!is_numeric($price) || $price < 0) {
        return '';
    }
    
    return $price;
}

// Get category breadcrumb
function getCategoryBreadcrumb($conn, $categoryId) {
    $breadcrumbs = [];
    
    if ($categoryId) {
        $sql = "SELECT * FROM categories WHERE id = " . mysqli_real_escape_string($conn, $categoryId);
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $category = mysqli_fetch_assoc($result);
            $breadcrumbs[] = $category;
        }
    }
    
    return $breadcrumbs;
}

// Get category statistics
function getCategoryStats($conn, $categoryId = null) {
    $sql = "SELECT 
                COUNT(*) as total_products,
                MIN(price) as min_price,
                MAX(price) as max_price,
                AVG(price) as avg_price
            FROM products 
            WHERE 1=1";
    
    if ($categoryId) {
        $categoryId = mysqli_real_escape_string($conn, $categoryId);
        $sql .= " AND category_id = '$categoryId'";
    }
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return [
        'total_products' => 0,
        'min_price' => 0,
        'max_price' => 0,
        'avg_price' => 0
    ];
}
?>
