<?php
// Category page with advanced filtering
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get category ID from URL
$categoryId = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : null;
$currentCategory = null;

// Get all categories for navigation
$categories = getAllCategories($conn);

// Get current category details
if ($categoryId) {
    $sql = "SELECT * FROM categories WHERE id = " . mysqli_real_escape_string($conn, $categoryId);
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $currentCategory = mysqli_fetch_assoc($result);
    } else {
        // Invalid category ID, redirect to all categories
        $categoryId = null;
    }
}

// Get filters from URL
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : '';
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$attributes = isset($_GET['attributes']) ? $_GET['attributes'] : [];

// Build filters array
$filters = [];
if (!empty($query)) {
    $filters['search'] = $query;
}
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

// Get price range
$priceRange = getPriceRange($conn, $categoryId);

// Get attributes for selected category
$categoryAttributes = [];
if (!empty($categoryId)) {
    $categoryAttributes = getAttributesByCategory($conn, $categoryId);
}
?>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="categories.php">Danh mục</a></li>
            <?php if ($currentCategory): ?>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($currentCategory['name']); ?></li>
            <?php else: ?>
                <li class="breadcrumb-item active">Tất cả sản phẩm</li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Category Quick Navigation -->
    <div class="mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Danh Mục Sản Phẩm</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="categories.php" class="btn <?php echo !$categoryId ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                Tất cả
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <a href="categories.php?category=<?php echo $cat['id']; ?>" 
                                   class="btn <?php echo ($categoryId == $cat['id']) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ Lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="categories.php" id="filterForm">
                        <?php if ($categoryId): ?>
                            <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                        <?php endif; ?>
                        
                        <!-- Search Filter -->
                        <div class="mb-3">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Nhập từ khóa..." value="<?php echo htmlspecialchars($query); ?>">
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
                            <a href="categories.php<?php echo $categoryId ? '?category='.$categoryId : ''; ?>" 
                               class="btn btn-outline-secondary">Xóa Bộ Lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>
                    <?php echo $currentCategory ? htmlspecialchars($currentCategory['name']) : 'Tất cả sản phẩm'; ?>
                    <small class="text-muted">(<?php echo $totalProducts; ?> sản phẩm)</small>
                </h2>
            </div>            <!-- Products Grid -->
            <div class="row">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text small text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...
                                </p>
                                <p class="product-price fw-bold text-primary"><?php echo formatPrice($product['price']); ?></p>
                                <?php if ($product['stock'] > 0): ?>
                                    <small class="text-success mb-2">Còn hàng: <?php echo $product['stock']; ?></small>
                                <?php else: ?>
                                    <small class="text-danger mb-2">Hết hàng</small>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between mt-auto">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">Xem Chi Tiết</a>
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
                            <a href="categories.php" class="btn btn-primary">Xem Tất Cả Sản Phẩm</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
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
