<?php
// Product detail page with variants support
require_once "includes/config.php";
require_once "includes/functions.php";
require_once "includes/category_functions.php";
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

// Get product variants
$variants = getProductVariants($conn, $id);

// Get category details
$category = null;
if ($product['category_id']) {
    $sql = "SELECT * FROM categories WHERE id = " . $product['category_id'];
    error_log("SQL query for category: " . $sql);
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
        error_log("Found category: " . print_r($category, true));
    } else {
        error_log("No category found for ID: " . $product['category_id']);
    }
} else {
    error_log("Product has no category_id set");
}

// Get category attributes for this product
$categoryAttributes = [];
if ($product['category_id']) {
    $categoryAttributes = getCategoryAttributes($conn, $product['category_id']);
    
    // Ghi log kiểm tra
    error_log("Category ID: " . $product['category_id']);
    error_log("Category Attributes: " . print_r($categoryAttributes, true));
}

// Get related products (same category)
$relatedProducts = [];
if ($product['category_id']) {
    error_log("Getting related products for category ID: " . $product['category_id']);
    $relatedProducts = getProductsByCategory($conn, $product['category_id']);
    // Remove current product from related products
    $relatedProducts = array_filter($relatedProducts, function($p) use ($id) {
        return $p['id'] != $id;
    });
    // Limit to 4 products
    $relatedProducts = array_slice($relatedProducts, 0, 4);
    error_log("Found related products: " . count($relatedProducts));
} else {
    // Fallback to old category system
    $categoryName = $product['category'];
    error_log("No category_id found, using category name: " . $categoryName);
    $sql = "SELECT * FROM products WHERE category = '" . mysqli_real_escape_string($conn, $categoryName) . "' AND id != $id LIMIT 4";
    error_log("SQL for related products: " . $sql);
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $relatedProducts[] = $row;
        }
    }
    error_log("Found related products by category name: " . count($relatedProducts));
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
            <img src="<?php echo $product['image']; ?>" class="img-fluid product-detail-image" alt="<?php echo $product['name']; ?>" onerror="this.src='https://via.placeholder.com/500x400?text=No+Image'">
        </div>
          <div class="col-md-7">            <div class="mb-2">
                <?php if ($category): ?>
                    <a href="categories.php?category=<?php echo $category['id']; ?>" class="badge bg-secondary text-decoration-none">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php elseif ($product['category']): ?>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span>
                <?php endif; ?>
            </div>
            
            <h1><?php echo $product['name']; ?></h1>
            
            <div class="product-price-section mb-3">
                <span class="fs-3 fw-bold text-primary" id="currentPrice"><?php echo formatPrice($product['price']); ?></span>
                <?php if (count($variants) > 0): ?>
                    <small class="text-muted d-block">Giá có thể thay đổi theo phiên bản</small>
                <?php endif; ?>
            </div>
            
            <p class="product-description"><?php echo $product['description']; ?></p>
            
            <!-- Product Variants -->
            <?php if (count($variants) > 0): ?>
            <div class="product-variants mb-4">
                <h5>Chọn phiên bản:</h5>
                <div class="row">
                    <?php foreach ($variants as $variant): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card variant-card" data-variant-id="<?php echo $variant['id']; ?>" 
                             data-price="<?php echo $variant['price']; ?>" 
                             data-stock="<?php echo $variant['stock']; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo $variant['variant_attributes']; ?></strong>
                                        <div class="text-muted small">SKU: <?php echo $variant['sku'] ?: 'N/A'; ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?php echo formatPrice($variant['price']); ?></div>
                                        <div class="small <?php echo $variant['stock'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $variant['stock'] > 0 ? 'Còn ' . $variant['stock'] : 'Hết hàng'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <!-- Stock info for products without variants -->
            <p class="stock-info">
                <strong>Tình trạng:</strong> 
                <?php if ($product['stock'] > 0): ?>
                    <span class="text-success">Còn hàng (<?php echo $product['stock']; ?> sản phẩm)</span>
                <?php else: ?>
                    <span class="text-danger">Hết hàng</span>
                <?php endif; ?>
            </p>
            <?php endif; ?>
            
            <!-- Add to Cart Form -->
            <form action="cart_actions.php" method="POST" class="mb-4" id="addToCartForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="variant_id" value="" id="selectedVariantId">
                
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-auto">
                        <label for="quantity" class="form-label">Số lượng:</label>
                    </div>
                    <div class="col-auto">
                        <select name="quantity" id="quantity" class="form-select">
                            <?php
                            $maxStock = count($variants) > 0 ? 0 : $product['stock'];
                            if (count($variants) > 0) {
                                foreach ($variants as $variant) {
                                    if ($variant['stock'] > $maxStock) {
                                        $maxStock = $variant['stock'];
                                    }
                                }
                            }
                            for ($i = 1; $i <= min(10, $maxStock); $i++): 
                            ?>
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
                    <img src="<?php echo $relatedProduct['image']; ?>" class="card-img-top product-image" alt="<?php echo $relatedProduct['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantCards = document.querySelectorAll('.variant-card');
    const currentPriceElement = document.getElementById('currentPrice');
    const selectedVariantIdInput = document.getElementById('selectedVariantId');
    const quantitySelect = document.getElementById('quantity');
    const addToCartForm = document.getElementById('addToCartForm');
    let selectedVariant = null;

    // Handle variant selection
    variantCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all cards
            variantCards.forEach(c => c.classList.remove('border-primary', 'selected-variant'));
            
            // Add active class to selected card
            this.classList.add('border-primary', 'selected-variant');
            
            // Get variant data
            const variantId = this.dataset.variantId;
            const price = parseFloat(this.dataset.price);
            const stock = parseInt(this.dataset.stock);
            
            selectedVariant = {
                id: variantId,
                price: price,
                stock: stock
            };
            
            // Update UI
            updateProductDisplay(price, stock);
            selectedVariantIdInput.value = variantId;
        });
    });

    function updateProductDisplay(price, stock) {
        // Update price display
        currentPriceElement.textContent = formatPrice(price);
        
        // Update quantity options
        quantitySelect.innerHTML = '';
        const maxQuantity = Math.min(10, stock);
        
        if (stock > 0) {
            for (let i = 1; i <= maxQuantity; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                quantitySelect.appendChild(option);
            }
            
            // Enable add to cart button
            const addToCartBtn = addToCartForm.querySelector('button[type="submit"]');
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="bi bi-cart-plus"></i> Thêm Vào Giỏ Hàng';
        } else {
            // Disable add to cart button
            const addToCartBtn = addToCartForm.querySelector('button[type="submit"]');
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="bi bi-x-circle"></i> Hết Hàng';
        }
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Validate form submission for products with variants
    addToCartForm.addEventListener('submit', function(e) {
        if (variantCards.length > 0 && !selectedVariant) {
            e.preventDefault();
            alert('Vui lòng chọn một phiên bản sản phẩm trước khi thêm vào giỏ hàng.');
            return false;
        }
    });
});
</script>
