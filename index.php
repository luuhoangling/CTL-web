<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Homepage
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get featured products
$featuredProducts = getAllProducts($conn);
// Show only first 4 products for homepage
$featuredProducts = array_slice($featuredProducts, 0, 4);
?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 fw-bold">Chào Mừng Đến Với Cửa Hàng Giày</h1>
                <p class="lead mb-4">Khám phá bộ sưu tập giày phong cách và thoải mái của chúng tôi</p>
                <a href="products.php" class="btn btn-primary btn-lg">Xem Tất Cả Sản Phẩm</a>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-4">Sản Phẩm Nổi Bật</h2>
    <div class="row">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="col-md-3 mb-4">
            <div class="card product-card">
                <img src="assets/images/<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                    <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                    <form action="cart_actions.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-success">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>    </div>
    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-primary">Xem Tất Cả Sản Phẩm</a>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center p-3">
                <div class="card-body">
                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                    <h4>Miễn Phí Vận Chuyển</h4>
                    <p>Cho đơn hàng trên 1.000.000đ</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center p-3">
                <div class="card-body">
                    <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                    <h4>Bảo Đảm Chất Lượng</h4>
                    <p>Đổi trả trong vòng 30 ngày</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center p-3">
                <div class="card-body">
                    <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                    <h4>Hỗ Trợ Khách Hàng</h4>
                    <p>Hỗ trợ 24/7</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
