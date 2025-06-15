<?php
// Products listing page
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get filters from URL
$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : '';
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : '';
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$attributes = isset($_GET['attributes']) ? $_GET['attributes'] : [];

// Build filters array
$filters = [];
if (!empty($categoryId)) {
    $filters['category_id'] = $categoryId;
}
if (!empty($priceMin)) {
    $filters['price_min'] = $priceMin;
}
if (!empty($priceMax)) {
    $filters['price_max'] = $priceMax;
}
if (!empty($sort)) {
    $filters['sort'] = $sort;
}
if (!empty($attributes)) {
    $filters['attributes'] = $attributes;
}

// Get products with filters
$products = getProductsWithFilters($conn, $filters);
$totalProducts = countProductsWithFilters($conn, $filters);

// Get all categories for filter
$categories = getAllCategories($conn);

// Get price range
$priceRange = getPriceRange($conn, $categoryId);

// Get attributes for selected category
$categoryAttributes = [];
if (!empty($categoryId)) {
    $categoryAttributes = getAttributesByCategory($conn, $categoryId);
}
?>

<div class="container my-4">
    <h1 class="mb-4">
        Tất Cả Sản Phẩm
        <small class="text-muted">(<?php echo $totalProducts; ?> sản phẩm)</small>
    </h1>
    
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ Lọc Sản Phẩm</h5>
                </div>
                <div class="card-body">
                    <form action="products.php" method="GET" id="filterForm">
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh Mục</label>
                            <select class="form-select" name="category_id" id="category" onchange="updateAttributeFilters()">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng Giá</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="price_min" 
                                           value="<?php echo $priceMin; ?>" placeholder="Từ" 
                                           min="<?php echo $priceRange['min_price']; ?>" 
                                           max="<?php echo $priceRange['max_price']; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="price_max" 
                                           value="<?php echo $priceMax; ?>" placeholder="Đến"
                                           min="<?php echo $priceRange['min_price']; ?>" 
                                           max="<?php echo $priceRange['max_price']; ?>">
                                </div>
                            </div>
                            <small class="text-muted">
                                Từ <?php echo formatPrice($priceRange['min_price']); ?> 
                                đến <?php echo formatPrice($priceRange['max_price']); ?>
                            </small>
                        </div>
                        
                        <!-- Attribute Filters -->
                        <?php if (!empty($categoryAttributes)): ?>
                            <?php foreach ($categoryAttributes as $attribute): ?>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo htmlspecialchars($attribute['name']); ?></label>
                                    <?php foreach ($attribute['values'] as $value): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="attributes[<?php echo $attribute['id']; ?>][]" 
                                                   value="<?php echo $value['id']; ?>"
                                                   id="attr_<?php echo $value['id']; ?>"
                                                   <?php echo (isset($attributes[$attribute['id']]) && in_array($value['id'], $attributes[$attribute['id']])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="attr_<?php echo $value['id']; ?>">
                                                <?php echo htmlspecialchars($value['value']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Sort Options -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sắp Xếp</label>
                            <select class="form-select" name="sort" id="sort">
                                <option value="">Mặc định</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                                <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Áp Dụng Lọc</button>
                            <a href="products.php" class="btn btn-outline-secondary">Xóa Bộ Lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products List -->
        <div class="col-md-9">
            <div class="row">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>" onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text small text-muted flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</p>
                                <p class="product-price fw-bold text-primary"><?php echo formatPrice($product['price']); ?></p>
                                <?php if ($product['stock'] > 0): ?>
                                    <small class="text-success mb-2">Còn hàng: <?php echo $product['stock']; ?></small>
                                <?php else: ?>
                                    <small class="text-danger mb-2">Hết hàng</small>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between mt-auto">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">Xem Chi Tiết</a>
                                    <?php if ($product['stock'] > 0): ?>
                                    <form action="cart_actions.php" method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Thêm Vào Giỏ</button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Hết hàng</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h4>Không tìm thấy sản phẩm nào</h4>
                            <p>Không có sản phẩm nào phù hợp với bộ lọc đã chọn.</p>
                            <a href="products.php" class="btn btn-primary">Xem Tất Cả Sản Phẩm</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateAttributeFilters() {
    const categoryId = document.getElementById('category').value;
    if (categoryId) {
        // Redirect to reload with new category to get attributes
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('category_id', categoryId);
        // Remove existing attribute filters
        const keysToRemove = [];
        for (const [key, value] of currentUrl.searchParams.entries()) {
            if (key.startsWith('attributes[')) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach(key => currentUrl.searchParams.delete(key));
        
        window.location.href = currentUrl.toString();
    }
}

// Auto-submit form when sort changes
document.getElementById('sort').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

// Auto-submit form when price range changes (with debounce)
let priceTimeout;
document.querySelectorAll('input[name="price_min"], input[name="price_max"]').forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(priceTimeout);
        priceTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 1000);
    });
});
</script>

<?php include "includes/footer.php"; ?>
