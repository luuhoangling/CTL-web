<?php
// Product detail page
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get product by ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    // Invalid product ID, redirect to products page
    header("Location: products.php");
    exit;
}

$product = getProductById($conn, $id);

if (!$product) {
    // Product not found, redirect to products page
    header("Location: products.php");
    exit;
}

// Get related products (same category)
$category = $product['category'];
$sql = "SELECT * FROM products WHERE category = '$category' AND id != $id LIMIT 4";
$result = mysqli_query($conn, $sql);
$relatedProducts = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $relatedProducts[] = $row;
    }
}
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang Chủ</a></li>
            <li class="breadcrumb-item"><a href="products.php">Sản Phẩm</a></li>
            <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 mb-4">
            <img src="assets/images/<?php echo $product['image']; ?>" class="img-fluid product-detail-image" alt="<?php echo $product['name']; ?>">
        </div>
        
        <div class="col-md-7">            <h1><?php echo $product['name']; ?></h1>
            <p class="product-price fs-3"><?php echo formatPrice($product['price']); ?></p>
            <div class="badge bg-secondary mb-3"><?php echo $product['category']; ?></div>
            
            <p><?php echo $product['description']; ?></p>
            
            <p>
                <strong>Tình trạng:</strong> 
                <?php if ($product['stock'] > 0): ?>
                    <span class="text-success">Còn hàng (<?php echo $product['stock']; ?> sản phẩm)</span>
                <?php else: ?>
                    <span class="text-danger">Hết hàng</span>
                <?php endif; ?>
            </p>
            
            <form action="cart_actions.php" method="POST" class="mb-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-auto">
                        <label for="quantity" class="form-label">Số lượng:</label>
                    </div>
                    <div class="col-auto">
                        <select name="quantity" id="quantity" class="form-select">
                            <?php for ($i = 1; $i <= min(10, $product['stock']); $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus"></i> Thêm Vào Giỏ Hàng
                </button>
            </form>
        </div>
    </div>
    
    <?php if (count($relatedProducts) > 0): ?>
    <div class="mt-5">
        <h3>Sản Phẩm Liên Quan</h3>
        <div class="row">            <?php foreach ($relatedProducts as $relatedProduct): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <img src="assets/images/<?php echo $relatedProduct['image']; ?>" class="card-img-top product-image" alt="<?php echo $relatedProduct['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $relatedProduct['name']; ?></h5>
                        <p class="product-price"><?php echo formatPrice($relatedProduct['price']); ?></p>
                        <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" class="btn btn-sm btn-outline-primary">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
