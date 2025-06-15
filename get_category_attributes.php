<?php
// AJAX endpoint to get category attributes
header('Content-Type: application/json');

require_once "includes/config.php";
require_once "includes/functions.php";

// Check if category_id is provided
if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    echo json_encode(['success' => false, 'message' => 'Category ID is required']);
    exit;
}

$categoryId = intval($_GET['category_id']);

try {
    // Get attributes for the category
    $attributes = getAttributesByCategory($conn, $categoryId);
    
    if (empty($attributes)) {
        echo json_encode(['success' => true, 'attributes' => []]);
    } else {
        echo json_encode(['success' => true, 'attributes' => $attributes]);
    }
    
} catch (Exception $e) {
    error_log("Error in get_category_attributes.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
