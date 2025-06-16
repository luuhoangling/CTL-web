<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Require login
requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $userId = $_SESSION['user_id'];
    
    // Validate email format first
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'invalid_format';
        exit;
    }
    
    $email = mysqli_real_escape_string($conn, $email);
    
    // Check if email is already used by another user
    $checkEmailSql = "SELECT id FROM users WHERE email = '$email' AND id != $userId";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    
    if (mysqli_num_rows($checkEmailResult) > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }
} else {
    echo 'error';
}
?>
