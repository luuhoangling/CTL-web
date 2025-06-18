<?php
require_once __DIR__ . '/session_config.php';

// DB config
define('DB_SERVER', 'sql301.infinityfree.com');
define('DB_USERNAME', 'if0_38993091');
define('DB_PASSWORD', 'QAAZcHm832nn');
define('DB_NAME', 'if0_38993091_shoe_store');

// Kết nối persistent MySQLi
$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
if (!mysqli_real_connect(
    $conn,
    'p:' . DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME
)) {
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
