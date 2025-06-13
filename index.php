<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Homepage
require_once "includes/config.php";
require_once "includes/functions.php";
require_once "includes/category_functions.php";
include "includes/header.php";

// Get categories for display
$categories = getAllCategories($conn);

// Get featured products
$featuredProducts = getAllProducts($conn);
// Show only first 8 products for homepage
$featuredProducts = array_slice($featuredProducts, 0, 8);
?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2" data-aos="fade-left" data-aos-duration="1200">
                <div class="text-center text-lg-end">
                    <img src="images/hero-image.png" alt="Hero Image" class="img-fluid rounded-3 shadow" style="max-height: 450px;">
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 py-5" data-aos="fade-right" data-aos-duration="1200">
                <h1 class="display-4 fw-bold mb-4">Chào Mừng Đến <span class="text-primary">CTL Store</span></h1>
                <p class="lead mb-4">Khám phá bộ sưu tập thời trang đa dạng, cập nhật xu hướng mới nhất với thiết kế độc đáo và chất lượng hàng đầu.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="categories.php" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-bag-heart me-2"></i>Khám Phá Ngay
                    </a>
                    <a href="products.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-grid me-2"></i>Xem Tất Cả
                    </a>
                </div>
                <div class="mt-4">
                    <div class="d-flex align-items-center text-muted">
                        <div class="me-3">
                            <i class="bi bi-truck text-primary fs-4"></i>
                            <span class="ms-2">Giao Hàng Nhanh</span>
                        </div>
                        <div class="me-3">
                            <i class="bi bi-shield-check text-primary fs-4"></i>
                            <span class="ms-2">Bảo Hành 1 Năm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<?php if (count($categories) > 0): ?>
<div class="container my-5">
    <div class="text-center mb-5" data-aos="fade-up" data-aos-duration="800">
        <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Danh Mục</span>
        <h2 class="display-5 fw-bold">Khám Phá Theo Danh Mục</h2>
        <div class="d-flex justify-content-center">
            <div class="col-lg-6">
                <p class="text-muted">Tìm kiếm sản phẩm theo từng danh mục để dễ dàng mua sắm và khám phá các xu hướng mới nhất</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($categories as $index => $category): ?>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
            <div class="card category-card h-100">
                <div class="card-body text-center p-4">
                    <div class="category-icon mb-3">
                        <?php
                        // Icon based on category name
                        $iconClass = 'bi-tag';
                        $categoryName = strtolower($category['name']);
                        if (strpos($categoryName, 'giày') !== false || strpos($categoryName, 'shoes') !== false) {
                            $iconClass = 'bi-shoe';
                        } elseif (strpos($categoryName, 'áo') !== false || strpos($categoryName, 'shirt') !== false) {
                            $iconClass = 'bi-person-square';
                        } elseif (strpos($categoryName, 'quần') !== false || strpos($categoryName, 'pants') !== false) {
                            $iconClass = 'bi-person-lines-fill';
                        }
                        ?>
                        <i class="<?php echo $iconClass; ?> display-4 text-primary"></i>
                    </div>
                    <h5 class="card-title fw-bold"><?php echo $category['name']; ?></h5>
                    <p class="card-text text-muted"><?php echo $category['description'] ?: 'Khám phá các sản phẩm ' . strtolower($category['name']); ?></p>
                    <a href="categories.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-2"></i>Xem Sản Phẩm
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="container my-5 py-4">
    <div class="text-center mb-5" data-aos="fade-up" data-aos-duration="800">
        <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Xu Hướng</span>
        <h2 class="display-5 fw-bold">Sản Phẩm Nổi Bật</h2>
        <div class="d-flex justify-content-center">
            <div class="col-lg-6">
                <p class="text-muted">Những sản phẩm được yêu thích nhất và bán chạy trong thời gian gần đây</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php foreach ($featuredProducts as $index => $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
            <div class="card product-card h-100">
                <div class="position-absolute top-0 end-0 p-2">
                    <?php if ($index < 3): ?>
                    <span class="badge bg-danger">Hot</span>
                    <?php endif; ?>
                </div>
                <div class="product-image-container">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>" 
                         onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <div class="mb-2">
                        <span class="badge bg-light text-dark">
                            <?php
                            if ($product['category_id']) {
                                $catSql = "SELECT name FROM categories WHERE id = " . $product['category_id'];
                                $catResult = mysqli_query($conn, $catSql);
                                if (mysqli_num_rows($catResult) > 0) {
                                    $catRow = mysqli_fetch_assoc($catResult);
                                    echo $catRow['name'];
                                }
                            } else {
                                echo $product['category'];
                            }
                            ?>
                        </span>
                    </div>
                    <h5 class="card-title fw-bold"><?php echo $product['name']; ?></h5>
                    <div class="mb-2">
                        <div class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="text-muted ms-2">(4.5)</span>
                        </div>
                    </div>
                    <p class="product-price mb-3"><?php echo formatPrice($product['price']); ?></p>
                    <div class="mt-auto d-flex gap-2">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary flex-grow-1">
                            <i class="bi bi-eye me-1"></i> Chi Tiết
                        </a>
                        <form action="cart_actions.php" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5" data-aos="fade-up">
        <a href="products.php" class="btn btn-primary btn-lg">
            <i class="bi bi-grid-3x3-gap me-2"></i>Xem Tất Cả Sản Phẩm
        </a>
    </div>
</div>
</div>

<div class="container-fluid py-5 my-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5" data-aos="fade-up">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Tại Sao Chọn Chúng Tôi</span>
                <h2 class="display-5 fw-bold mb-4">Dịch Vụ Khách Hàng Tuyệt Vời</h2>
                <p class="text-muted">Chúng tôi cam kết mang đến trải nghiệm mua sắm tốt nhất cho khách hàng</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm text-center p-4 h-100 feature-card">
                    <div class="card-body">
                        <div class="feature-icon-wrapper mb-4">
                            <i class="bi bi-truck fs-1 text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Miễn Phí Vận Chuyển</h4>
                        <p class="text-muted">Giao hàng miễn phí toàn quốc cho đơn hàng từ 500.000đ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm text-center p-4 h-100 feature-card">
                    <div class="card-body">
                        <div class="feature-icon-wrapper mb-4">
                            <i class="bi bi-shield-check fs-1 text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Bảo Đảm Chất Lượng</h4>
                        <p class="text-muted">Bảo hành 1 năm và đổi trả miễn phí trong 30 ngày đầu tiên</p>
                    </div>
                </div>
            </div>            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm text-center p-4 h-100 feature-card">
                    <div class="card-body">
                        <div class="feature-icon-wrapper mb-4">
                            <i class="bi bi-headset fs-1 text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Hỗ Trợ Khách Hàng</h4>
                        <p class="text-muted">Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ 24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div class="container my-5 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-6 bg-primary text-white p-5 d-flex align-items-center" data-aos="fade-right">
                        <div>
                            <h2 class="fw-bold mb-3">Đăng Ký Nhận Thông Tin</h2>
                            <p class="mb-4">Nhận thông tin về các chương trình khuyến mãi, sản phẩm mới và ưu đãi đặc biệt!</p>
                            <form class="newsletter-form">
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control" placeholder="Email của bạn" required>
                                    <button class="btn btn-light" type="submit">Đăng ký</button>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletterConsent">
                                    <label class="form-check-label small" for="newsletterConsent">
                                        Tôi đồng ý nhận các thông tin khuyến mãi qua email
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-left">
                        <div class="h-100 d-flex align-items-center justify-content-center p-4">
                            <img src="images/newsletter-image.png" alt="Newsletter" class="img-fluid" style="max-height: 250px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
