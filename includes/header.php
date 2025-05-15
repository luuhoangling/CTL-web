<?php
// Start the session on every page
session_start();

// Cart initialization
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Giày</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">Cửa Hàng Giày</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Trang Chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Sản Phẩm</a>
                        </li>
                    </ul>
                    <form class="d-flex mx-3" action="search.php" method="GET">
                        <input class="form-control me-2" type="search" name="q" placeholder="Tìm kiếm sản phẩm...">
                        <button class="btn btn-outline-light" type="submit">Tìm Kiếm</button>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="bi bi-cart"></i> Giỏ Hàng 
                                <?php
                                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    echo '<span class="badge bg-danger">' . array_sum(array_column($_SESSION['cart'], 'quantity')) . '</span>';
                                }
                                ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container py-4">
