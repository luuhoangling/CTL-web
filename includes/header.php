<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cart initialization
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get categories for navigation menu
$headerCategories = [];
if (isset($conn)) {
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $headerCategories[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng CTL - Thời Trang Đa Dạng</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="announcement-bar py-1 bg-dark text-white">
        <div class="container text-center">
            <small>
                <i class="bi bi-stars"></i> Freeship cho đơn hàng từ 500K 
                <i class="bi bi-dot"></i> 
                <i class="bi bi-badge-ad"></i> Giảm 20% cho đơn hàng đầu tiên
                <i class="bi bi-dot"></i>
                <i class="bi bi-arrow-repeat"></i> 30 ngày đổi trả miễn phí
            </small>
        </div>
    </div>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container position-relative">
                <!-- Logo -->
                <a class="navbar-brand me-3" href="index.php" data-aos="fade-right" data-aos-duration="1000">
                    <img src="images/logo.png" alt="CTL Logo" height="60" class="header-logo">
                </a>
                
                <!-- Toggle button cho mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Left and right side nav content -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Links bên trái -->
                    <div class="navbar-nav me-auto">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Trang Chủ</a>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-grid me-1"></i>Sản phẩm
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="categories.php">Tất cả sản phẩm</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php foreach ($headerCategories as $cat): ?>
                                    <li>
                                        <a class="dropdown-item" href="categories.php?category=<?php echo $cat['id']; ?>">
                                            <?php echo $cat['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <!-- <a class="nav-link" href="products.php">Sản Phẩm</a> -->
                    </div>                    <!-- Khung tìm kiếm bên phải -->
                    <form class="search-form ms-auto me-2" action="search.php" method="GET">
                        <div class="input-group">
                            <input class="form-control search-input" type="search" name="q" placeholder="Tìm kiếm sản phẩm...">
                            <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item me-2">
                            <a class="nav-link position-relative" href="cart.php" data-aos="fade-left" data-aos-duration="800">
                                <i class="bi bi-cart-fill"></i> Giỏ Hàng
                                <?php
                                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                    echo '<span class="badge bg-danger position-absolute" style="top: 0; right: 0; transform: translate(25%, -25%);">' . array_sum(array_column($_SESSION['cart'], 'quantity')) . '</span>';
                                }
                                ?>
                            </a>
                        </li>                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item dropdown" data-aos="fade-left" data-aos-duration="900">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <div class="px-3 py-2 mb-2 text-center border-bottom">
                                        <span class="d-block text-primary fw-bold"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></small>
                                    </div>
                                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Thông tin cá nhân</a></li>
                                    <li><a class="dropdown-item" href="orders.php"><i class="bi bi-bag me-2"></i>Đơn hàng của tôi</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item" data-aos="fade-left" data-aos-duration="900">
                                <a class="nav-link" href="login.php">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                                </a>
                            </li>
                            <li class="nav-item" data-aos="fade-left" data-aos-duration="1000">
                                <a class="nav-link btn btn-sm btn-outline-light rounded-pill px-3 ms-2" href="register.php">
                                    <i class="bi bi-person-plus me-1"></i> Đăng ký
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>                </div>
            </div>
        </nav>
    </header>
    
    <!-- Scroll to top button -->
    <button id="scrollToTop" class="scroll-to-top">
        <i class="bi bi-arrow-up"></i>
    </button>
    
    <main class="container py-4"></main>