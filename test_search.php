<?php
// Test file for search functionality
require_once "includes/config.php";
require_once "includes/functions.php";

// Enable error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Search Function Test</h1>";

// Test 1: Basic search
echo "<h2>Test 1: Basic search for 'thể thao'</h2>";
$filters1 = ['search' => 'thể thao'];
try {
    $products1 = getProductsWithFilters($conn, $filters1);
    echo "Found " . count($products1) . " products<br>";
    foreach ($products1 as $product) {
        echo "- " . htmlspecialchars($product['name']) . " (ID: " . $product['id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 2: Category filter
echo "<h2>Test 2: Category filter (ID = 1)</h2>";
$filters2 = ['category_id' => 1];
try {
    $products2 = getProductsWithFilters($conn, $filters2);
    echo "Found " . count($products2) . " products<br>";
    foreach ($products2 as $product) {
        echo "- " . htmlspecialchars($product['name']) . " (Category: " . $product['category_id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Combined search and category
echo "<h2>Test 3: Combined search 'thể thao' + category 1</h2>";
$filters3 = ['search' => 'thể thao', 'category_id' => 1];
try {
    $products3 = getProductsWithFilters($conn, $filters3);
    echo "Found " . count($products3) . " products<br>";
    foreach ($products3 as $product) {
        echo "- " . htmlspecialchars($product['name']) . " (Category: " . $product['category_id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Check categories
echo "<h2>Test 4: Available categories</h2>";
try {
    $categories = getAllCategories($conn);
    echo "Found " . count($categories) . " categories<br>";
    foreach ($categories as $category) {
        echo "- " . htmlspecialchars($category['name']) . " (ID: " . $category['id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 5: Check products table structure
echo "<h2>Test 5: Products table structure</h2>";
$sql = "DESCRIBE products";
$result = mysqli_query($conn, $sql);
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing products table: " . mysqli_error($conn);
}

echo "<hr>";

// Test 6: Sample products
echo "<h2>Test 6: Sample products in database</h2>";
$sql = "SELECT id, name, category_id, price FROM products LIMIT 10";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category ID</th><th>Price</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . $row['category_id'] . "</td>";
        echo "<td>" . number_format($row['price']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No products found or error: " . mysqli_error($conn);
}
?>
