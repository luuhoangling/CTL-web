<?php
// Database connection configuration
define('DB_SERVER', 'sql301.infinityfree.com');
define('DB_USERNAME', 'if0_38993091');
define('DB_PASSWORD', 'QAAZcHm832nn');
define('DB_NAME', 'if0_38993091_shoe_store');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}
?>
