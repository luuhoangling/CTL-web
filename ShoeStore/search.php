<?php
// Search page
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Search products
$products = [];
if (!empty($query)) {
    $products = searchProducts($conn, $query);
}
?>

<div class="container my-4">    <h1 class="mb-4">Kết Quả Tìm Kiếm cho "<?php echo htmlspecialchars($query); ?>"</h1>
    
    <form action="search.php" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Tìm kiếm sản phẩm...">
            <button class="btn btn-primary" type="submit">Tìm Kiếm</button>
        </div>
    </form>
    
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <img src="assets/images/<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text small text-muted"><?php echo substr($product['description'], 0, 60); ?>...</p>
                        <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                        <div class="d-flex justify-content-between">                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">Xem Chi Tiết</a>
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-success">Thêm Vào Giỏ</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">                    <?php if (empty($query)): ?>
                        Vui lòng nhập từ khóa tìm kiếm.
                    <?php else: ?>
                        Không tìm thấy sản phẩm nào phù hợp với "<?php echo htmlspecialchars($query); ?>".
                    <?php endif; ?>
                </div>
                <a href="products.php" class="btn btn-primary">Xem Tất Cả Sản Phẩm</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
