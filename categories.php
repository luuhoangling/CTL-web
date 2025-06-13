<?php
// Category page with advanced filtering
require_once "includes/config.php";
require_once "includes/functions.php";
require_once "includes/category_functions.php";
require_once "includes/category_components.php";
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

// Prepare filters
$filters = [
    'category_id' => $categoryId,
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'min_price' => isset($_GET['min_price']) ? $_GET['min_price'] : '',
    'max_price' => isset($_GET['max_price']) ? $_GET['max_price'] : '',
    'attributes' => isset($_GET['attr']) ? $_GET['attr'] : [],
    'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'newest'
];

// Get filtered products
$products = filterProducts($conn, $filters);

// Get category attributes for filtering
$categoryAttributes = [];
if ($categoryId) {
    $categoryAttributes = getCategoryAttributes($conn, $categoryId);
}

// Get price range
$priceRange = getCategoryPriceRange($conn, $categoryId);
?>

<div class="container my-4">
    <!-- Category Quick Navigation -->
    <?php renderCategoryNavigation($categories, $categoryId); ?>
    
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

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="">
                        <?php if ($categoryId): ?>
                            <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                        <?php endif; ?>
                          <!-- Search Filter -->
                        <div class="mb-3">
                            <h6>Tìm kiếm</h6>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Nhập từ khóa..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                        </div>
                        
                        <!-- Category Filter -->
                        <?php if (!$categoryId): ?>
                        <div class="mb-3">
                            <h6>Danh mục</h6>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>                        <!-- Price Range Filter -->
                        <?php renderPriceRangeSlider(
                            $priceRange['min_price'], 
                            $priceRange['max_price'], 
                            $filters['min_price'], 
                            $filters['max_price']
                        ); ?>                        <!-- Attribute Filters -->
                        <?php renderAttributeFilters($categoryAttributes, $filters['attributes']); ?>

                        <button type="submit" class="btn btn-primary btn-sm w-100">Áp dụng bộ lọc</button>
                        <a href="categories.php<?php echo $categoryId ? '?category='.$categoryId : ''; ?>" 
                           class="btn btn-outline-secondary btn-sm w-100 mt-2">Xóa bộ lọc</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>
                    <?php echo $currentCategory ? $currentCategory['name'] : 'Tất cả sản phẩm'; ?>
                    <small class="text-muted">(<?php echo count($products); ?> sản phẩm)</small>
                </h1>
                
                <!-- Sorting -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" 
                            id="sortDropdown" data-bs-toggle="dropdown">
                        Sắp xếp
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest'])); ?>">Mới nhất</a></li>
                        <li><a class="dropdown-item" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_asc'])); ?>">Giá thấp đến cao</a></li>
                        <li><a class="dropdown-item" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_desc'])); ?>">Giá cao đến thấp</a></li>
                        <li><a class="dropdown-item" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name'])); ?>">Tên A-Z</a></li>
                    </ul>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" 
                                 alt="<?php echo $product['name']; ?>" 
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <small class="text-muted"><?php echo $product['category_name']; ?></small>
                                </div>
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text small text-muted flex-grow-1">
                                    <?php echo substr($product['description'], 0, 80); ?>...
                                </p>
                                <div class="mt-auto">
                                    <p class="product-price mb-2"><?php echo formatPrice($product['price']); ?></p>
                                    <div class="d-flex justify-content-between">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                        <form action="cart_actions.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Thêm vào giỏ</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <h4>Không tìm thấy sản phẩm nào</h4>
                            <p>Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                            <a href="categories.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
