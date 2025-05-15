<?php
// Check if admin is logged in
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    // Check if we're in the admin folder or a subfolder
    $path = dirname($_SERVER['PHP_SELF']);
    if (strpos($path, '/admin') !== false && $path != '/admin') {
        header("location: ../auth/login.php");
    } else {
        header("location: auth/login.php");
    }
    exit;
}

// Define variables to handle paths
$current_page = basename($_SERVER['PHP_SELF']);
$admin_root = '';

// Check if we're in a subfolder of admin
if (strpos($_SERVER['PHP_SELF'], '/admin/products/') !== false || 
    strpos($_SERVER['PHP_SELF'], '/admin/auth/') !== false) {
    $admin_root = '../';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị - Cửa Hàng Giày</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $admin_root; ?>../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $admin_root; ?>index.php">Quản Trị Cửa Hàng Giày</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>index.php">Bảng Điều Khiển</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($current_page, 'products') !== false) ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>products/index.php">Sản Phẩm</a>
                    </li>                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $admin_root; ?>../index.php" target="_blank">Xem Trang Web</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $admin_root; ?>auth/logout.php">Đăng Xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block bg-light sidebar admin-sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>index.php">
                                <i class="bi bi-speedometer2"></i> Bảng Điều Khiển
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($current_page, 'index.php') !== false && strpos($_SERVER['PHP_SELF'], 'products')) ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>products/index.php">
                                <i class="bi bi-grid"></i> Sản Phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'create.php') ? 'active' : ''; ?>" href="<?php echo $admin_root; ?>products/create.php">
                                <i class="bi bi-plus-circle"></i> Thêm Sản Phẩm
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-md-4 py-4 admin-content">
