<?php
// Include session configuration first
require_once __DIR__ . '/session_config.php';

// Database connection configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'shoe_store');

// Set charset for database connection
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
mysqli_real_connect($conn, DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if(mysqli_connect_errno()){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
