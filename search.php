<?php
// Search page
require_once "includes/config.php";
require_once "includes/functions.php";

// Enable error reporting for debugging (remove in production)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include "includes/header.php";

// Get search query and filters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : '';
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : '';
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$attributes = isset($_GET['attributes']) ? $_GET['attributes'] : [];

// Validate price range
$priceError = '';
if (!empty($priceMin) && !empty($priceMax) && $priceMin > $priceMax) {
    $priceError = 'Giá tối thiểu không thể lớn hơn giá tối đa. Vui lòng nhập lại khoảng giá hợp lệ.';
    // Reset invalid price values
    $priceMin = '';
    $priceMax = '';
}

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

// Debug: Log the filters
error_log("Search filters: " . print_r($filters, true));

try {
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

    // Debug: Log the results
    error_log("Products found: " . count($products));
    error_log("Total products: " . $totalProducts);

} catch (Exception $e) {
    error_log("Error in search.php: " . $e->getMessage());

    // Set default values to prevent further errors
    $products = [];
    $totalProducts = 0;
    $categories = getAllCategories($conn) ?? [];
    $priceRange = ['min_price' => 0, 'max_price' => 0];
    $categoryAttributes = [];

    // Show user-friendly error message
    echo '<div class="container my-4">';
    echo '<div class="alert alert-warning" role="alert">';
    echo '<h4 class="alert-heading">Có lỗi xảy ra!</h4>';
    echo '<p>Đã xảy ra lỗi khi tìm kiếm sản phẩm. Vui lòng thử lại hoặc liên hệ quản trị viên.</p>';
    echo '<hr>';
    echo '<p class="mb-0">Mã lỗi: <code>' . htmlspecialchars($e->getMessage()) . '</code></p>';
    echo '</div>';
    echo '</div>';
}
?>

<div class="container my-4">    
    <?php if (!empty($priceError)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert" id="price-error-alert" data-testid="price-error-alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Lỗi khoảng giá:</strong> <?php echo htmlspecialchars($priceError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <h1 class="mb-4 hidden" id="search-results-title" data-testid="search-results-title">
        <?php if (!empty($query)): ?>
            Kết Quả Tìm Kiếm cho "<?php echo htmlspecialchars($query); ?>"
        <?php else: ?>
            Tất Cả Sản Phẩm
        <?php endif; ?>        <small class="text-muted" id="products-count" data-testid="products-count">(<?php echo $totalProducts; ?> sản phẩm)</small>
    </h1>

    <div class="row"> <!-- Filter Sidebar -->
        <div class="col-md-3" id="filter-sidebar" data-testid="filter-sidebar">
            <div class="card" id="filter-card" data-testid="filter-card">
                <div class="card-header">
                    <h5 class="mb-0" id="filter-title" data-testid="filter-title">Bộ Lọc Tìm Kiếm</h5>
                </div>
                <div class="card-body">
                    <form action="search.php" method="GET" id="product-filter-form" data-testid="product-filter-form">
                        <!-- Search Box -->
                        <div class="mb-3" id="search-input-section" data-testid="search-input-section">
                            <label for="search-product-input" class="form-label">Từ khóa tìm kiếm</label>
                            <input type="text" class="form-control" name="q" id="search-product-input"
                                data-testid="search-input" value="<?php echo htmlspecialchars($query); ?>"
                                placeholder="Tìm kiếm sản phẩm...">
                        </div>
                        <!-- Category Filter -->
                        <div class="mb-3" id="category-filter-section" data-testid="category-filter-section">
                            <label for="category-filter-select" class="form-label">Danh Mục</label> <select
                                class="form-select" name="category_id" id="category-filter-select"
                                data-testid="category-select">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div> <!-- Price Range Filter -->
                        <div class="mb-3" id="price-range-section" data-testid="price-range-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Khoảng Giá <small class="text-muted">(Tùy
                                        chọn)</small></label>
                                <?php if (!empty($priceMin) || !empty($priceMax)): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="clearPriceRange()" data-testid="clear-price-range">
                                        <i class="fas fa-times"></i> Xóa
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="price_min"
                                        id="price-min-input" data-testid="price-min" value="<?php echo $priceMin; ?>"
                                        placeholder="Giá tối thiểu" min="0"
                                        max="<?php echo $priceRange['max_price']; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" name="price_max"
                                        id="price-max-input" data-testid="price-max" value="<?php echo $priceMax; ?>"
                                        placeholder="Giá tối đa" min="0" max="<?php echo $priceRange['max_price']; ?>">
                                </div>
                            </div>
                            <small class="text-muted" id="price-range-info" data-testid="price-range-info">
                                Khoảng giá hiện tại: <?php echo formatPrice($priceRange['min_price']); ?>
                                - <?php echo formatPrice($priceRange['max_price']); ?>
                            </small>
                        </div> <!-- Attribute Filters -->
                        <div id="attribute-filters-section" data-testid="attribute-filters-section">
                            <?php if (!empty($categoryAttributes)): ?>
                                <?php foreach ($categoryAttributes as $attribute): ?>
                                    <div class="mb-3" id="attribute-group-<?php echo $attribute['id']; ?>"
                                        data-testid="attribute-group-<?php echo strtolower(str_replace(' ', '-', $attribute['name'])); ?>">
                                        <label class="form-label"><?php echo htmlspecialchars($attribute['name']); ?></label>
                                        <?php foreach ($attribute['values'] as $value): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="attributes[<?php echo $attribute['id']; ?>][]"
                                                    value="<?php echo $value['id']; ?>" id="attr_<?php echo $value['id']; ?>"
                                                    data-testid="attribute-<?php echo strtolower(str_replace(' ', '-', $attribute['name'])); ?>-<?php echo strtolower(str_replace(' ', '-', $value['value'])); ?>"
                                                    <?php echo (isset($attributes[$attribute['id']]) && in_array($value['id'], $attributes[$attribute['id']])) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="attr_<?php echo $value['id']; ?>">
                                                    <?php echo htmlspecialchars($value['value']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <!-- Sort Options -->
                        <div class="mb-3" id="sort-section" data-testid="sort-section">
                            <label for="sort-products-select" class="form-label">Sắp Xếp</label>
                            <select class="form-select" name="sort" id="sort-products-select" data-testid="sort-select">
                                <option value="">Mặc định</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng
                                    dần</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm
                                    dần</option>
                                <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Tên A-Z
                                </option>
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất
                                </option>
                            </select>
                        </div>
                        <div class="d-grid gap-2" id="filter-buttons" data-testid="filter-buttons">
                            <button type="submit" class="btn btn-primary" id="apply-filters-btn"
                                data-testid="apply-filters">
                                <i class="fas fa-search"></i> Áp Dụng Lọc
                            </button>
                            <a href="search.php" class="btn btn-outline-secondary" id="clear-filters-btn"
                                data-testid="clear-filters">
                                <i class="fas fa-times"></i> Xóa Bộ Lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Products List -->
        <div class="col-md-9" id="products-section" data-testid="products-section">
            <div class="row" id="products-grid" data-testid="products-grid">
                <?php if (count($products) > 0): ?>     <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-testid="product-card-wrapper-<?php echo $product['id']; ?>">
                            <div class="card product-card h-100" id="product-card-<?php echo $product['id']; ?>"
                                data-testid="product-card-<?php echo $product['id']; ?>">
                                <img src="<?php echo $product['image']; ?>" class="card-img-top product-image"
                                    alt="<?php echo $product['name']; ?>"
                                    onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'"
                                    data-testid="product-image-<?php echo $product['id']; ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title" data-testid="product-name-<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h5>
                                    <p class="card-text small text-muted flex-grow-1"
                                        data-testid="product-description-<?php echo $product['id']; ?>">
                                        <?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...
                                    </p>
                                    <p class="product-price fw-bold text-primary"
                                        data-testid="product-price-<?php echo $product['id']; ?>">
                                        <?php echo formatPrice($product['price']); ?>
                                    </p>
                                    <?php if ($product['stock'] > 0): ?>
                                        <small class="text-success mb-2"
                                            data-testid="product-stock-<?php echo $product['id']; ?>">Còn hàng:
                                            <?php echo $product['stock']; ?></small>
                                    <?php else: ?>
                                        <small class="text-danger mb-2"
                                            data-testid="product-stock-<?php echo $product['id']; ?>">Hết hàng</small>
                                    <?php endif; ?>                                    <div class="d-flex justify-content-between mt-auto">
                                        <a href="product.php?id=<?php echo $product['id']; ?>"
                                            class="btn btn-xs btn-outline-primary"
                                            data-testid="view-product-<?php echo $product['id']; ?>">Xem Chi Tiết</a>
                                        <?php if ($product['stock'] > 0): ?>
                                            <form action="cart_actions.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-xs btn-success"
                                                    data-testid="add-to-cart-<?php echo $product['id']; ?>">Thêm Vào Giỏ</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-xs btn-secondary" disabled
                                                data-testid="out-of-stock-<?php echo $product['id']; ?>">Hết hàng</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?> <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center" id="no-products-message"
                            data-testid="no-products-message">
                            <h4 data-testid="no-products-title">Không tìm thấy sản phẩm nào</h4>
                            <?php if (!empty($query)): ?>
                                <p data-testid="no-products-search-text">Không tìm thấy sản phẩm nào phù hợp với
                                    "<?php echo htmlspecialchars($query); ?>" và các bộ lọc đã chọn.</p>
                            <?php else: ?>
                                <p data-testid="no-products-filter-text">Không có sản phẩm nào phù hợp với bộ lọc đã chọn.</p>
                            <?php endif; ?>
                            <a href="search.php" class="btn btn-primary" data-testid="view-all-products">Xem Tất Cả Sản
                                Phẩm</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- No Products Found Message (Initially Hidden) -->
            <div class="col-12" id="no-products-filter-message" style="display: none;"
                data-testid="no-products-filter-message" data-no-auto-dismiss="true">
                <div class="alert alert-warning text-center" data-no-auto-dismiss="true">
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    </div>
                    <h4 data-testid="no-filter-results-title">Không tìm thấy sản phẩm phù hợp</h4>
                    <p class="mb-3" data-testid="no-filter-results-text">
                        Không có sản phẩm nào phù hợp với các bộ lọc bạn đã chọn.
                        Vui lòng thử điều chỉnh bộ lọc hoặc xóa một số điều kiện tìm kiếm.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-primary" onclick="clearAllFilters()"
                            data-testid="clear-all-filters-btn">
                            <i class="fas fa-times"></i> Xóa Tất Cả Bộ Lọc
                        </button>
                        <a href="search.php" class="btn btn-primary" data-testid="browse-all-products">
                            <i class="fas fa-eye"></i> Xem Tất Cả Sản Phẩm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>    // Function to check if there are any products and show no products message
    function checkProductsVisibility() {
        const productsGrid = document.getElementById('products-grid');
        const noProductsMessage = document.getElementById('no-products-filter-message');
        const productCards = productsGrid.querySelectorAll('[data-testid*="product-card-"]');
        
        // Count visible product cards
        let visibleProducts = 0;
        productCards.forEach(card => {
            if (card.style.display !== 'none' && !card.classList.contains('d-none')) {
                visibleProducts++;
            }
        });
        
        // Show no products message only when no products are visible
        if (visibleProducts === 0 && productCards.length > 0) {
            // Products exist but none are visible due to filtering
            noProductsMessage.style.display = 'block';
        }
        
        // Only update products count if we're doing client-side filtering
        // Don't interfere with the initial server-side count
        const productsCount = document.getElementById('products-count');
        if (productsCount && window.isClientSideFiltering) {
            productsCount.textContent = `(${visibleProducts} sản phẩm)`;
        }
        
        return visibleProducts;
    }    // Function to clear all filters
    function clearAllFilters() {
        // Reset client-side filtering flag
        window.isClientSideFiltering = false;
        
        // Clear search input
        const searchInput = document.getElementById('search-product-input');
        if (searchInput) searchInput.value = '';
        
        // Reset category selection
        const categorySelect = document.getElementById('category-filter-select');
        if (categorySelect) categorySelect.value = '';
        
        // Clear price inputs
        const priceMinInput = document.getElementById('price-min-input');
        const priceMaxInput = document.getElementById('price-max-input');
        if (priceMinInput) priceMinInput.value = '';
        if (priceMaxInput) priceMaxInput.value = '';
        
        // Clear all attribute checkboxes
        const attributeCheckboxes = document.querySelectorAll('[name^="attributes["]');
        attributeCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset sort selection
        const sortSelect = document.getElementById('sort-products-select');
        if (sortSelect) sortSelect.value = '';
        
        // Clear attributes section
        const attributeSection = document.getElementById('attribute-filters-section');
        if (attributeSection) attributeSection.innerHTML = '';
        
        // Show all products
        const productCards = document.querySelectorAll('[data-testid*="product-card-wrapper-"]');
        productCards.forEach(card => {
            card.style.display = 'block';
            card.classList.remove('d-none');
        });
        
        // Manually hide no products message when clearing filters
        const noProductsMessage = document.getElementById('no-products-filter-message');
        if (noProductsMessage) noProductsMessage.style.display = 'none';
        
        // Reset products count to original server-side count
        const productsCount = document.getElementById('products-count');
        if (productsCount) {
            const originalCount = productCards.length;
            productsCount.textContent = `(${originalCount} sản phẩm)`;
        }
          // Update products count
        checkProductsVisibility();
        
        // Update title visibility
        updateSearchTitleVisibility();
        
        // Show visual feedback
        const clearBtn = document.querySelector('[data-testid="clear-all-filters-btn"]');
        if (clearBtn) {
            const originalText = clearBtn.innerHTML;
            clearBtn.innerHTML = '<i class="fas fa-check"></i> Đã xóa tất cả bộ lọc';
            clearBtn.classList.add('btn-success');
            clearBtn.classList.remove('btn-outline-primary');
            
            setTimeout(() => {
                clearBtn.innerHTML = originalText;
                clearBtn.classList.remove('btn-success');
                clearBtn.classList.add('btn-outline-primary');
            }, 2000);
        }
    }// Function to filter products by client-side (for real-time filtering)
    function filterProductsClientSide() {
        // Mark that we're doing client-side filtering
        window.isClientSideFiltering = true;
        
        const searchQuery = document.getElementById('search-product-input').value.toLowerCase();
        const selectedCategory = document.getElementById('category-filter-select').value;
        const priceMin = parseFloat(document.getElementById('price-min-input').value) || 0;
        const priceMax = parseFloat(document.getElementById('price-max-input').value) || Infinity;
        
        const productCards = document.querySelectorAll('[data-testid*="product-card-wrapper-"]');
        
        productCards.forEach(cardWrapper => {
            const card = cardWrapper.querySelector('[data-testid*="product-card-"]');
            if (!card) return;
            
            const productName = card.querySelector('[data-testid*="product-name-"]');
            const productDescription = card.querySelector('[data-testid*="product-description-"]');
            const productPrice = card.querySelector('[data-testid*="product-price-"]');
            
            let shouldShow = true;
            
            // Search query filter
            if (searchQuery && productName && productDescription) {
                const nameText = productName.textContent.toLowerCase();
                const descText = productDescription.textContent.toLowerCase();
                if (!nameText.includes(searchQuery) && !descText.includes(searchQuery)) {
                    shouldShow = false;
                }
            }
            
            // Price range filter
            if (productPrice && shouldShow) {
                const priceText = productPrice.textContent.replace(/[^\d]/g, '');
                const price = parseFloat(priceText);
                if (price < priceMin || price > priceMax) {
                    shouldShow = false;
                }
            }
            
            // Show/hide product card
            if (shouldShow) {
                cardWrapper.style.display = 'block';
                cardWrapper.classList.remove('d-none');
            } else {
                cardWrapper.style.display = 'none';
                cardWrapper.classList.add('d-none');
            }
        });
        
        // Only check visibility but don't auto-hide message
        const visibleCount = checkProductsVisibility();
          // Show no products message only if no products are visible
        const noProductsMessage = document.getElementById('no-products-filter-message');
        if (visibleCount === 0 && productCards.length > 0 && noProductsMessage) {
            noProductsMessage.style.display = 'block';
        }
        
        // Update title visibility
        updateSearchTitleVisibility();
    }

    // Function to load attributes via AJAX when category changes
    function loadCategoryAttributes(categoryId) {
        const attributeSection = document.getElementById('attribute-filters-section');

        if (!categoryId) {
            attributeSection.innerHTML = '';
            return;
        }

        // Show loading state
        attributeSection.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải thuộc tính...</div>';

        // Create AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `get_category_attributes.php?category_id=${categoryId}`, true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            let attributeHTML = '';

                            response.attributes.forEach(attribute => {
                                attributeHTML += `
                                <div class="mb-3" id="attribute-group-${attribute.id}" data-testid="attribute-group-${attribute.name.toLowerCase().replace(/\s+/g, '-')}">
                                    <label class="form-label">${attribute.name}</label>
                            `;

                                attribute.values.forEach(value => {
                                    attributeHTML += `
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="attributes[${attribute.id}][]" 
                                               value="${value.id}"
                                               id="attr_${value.id}"
                                               data-testid="attribute-${attribute.name.toLowerCase().replace(/\s+/g, '-')}-${value.value.toLowerCase().replace(/\s+/g, '-')}">
                                        <label class="form-check-label" for="attr_${value.id}">
                                            ${value.value}
                                        </label>
                                    </div>
                                `;
                                });

                                attributeHTML += '</div>';
                            });

                            attributeSection.innerHTML = attributeHTML;
                        } else {
                            attributeSection.innerHTML = '<div class="text-muted"><small>Không có thuộc tính nào cho danh mục này</small></div>';
                        }
                    } catch (e) {
                        attributeSection.innerHTML = '<div class="text-danger"><small>Có lỗi khi tải thuộc tính</small></div>';
                    }
                } else {
                    attributeSection.innerHTML = '<div class="text-danger"><small>Có lỗi khi tải thuộc tính</small></div>';
                }
            }
        };

        xhr.send();
    }

    // Category change handler
    document.getElementById('category-filter-select').addEventListener('change', function () {
        const categoryId = this.value;
        loadCategoryAttributes(categoryId);
    });

    // Function to clear price range
    function clearPriceRange() {
        document.getElementById('price-min-input').value = '';
        document.getElementById('price-max-input').value = '';

        // Show visual feedback
        const clearBtn = document.querySelector('[data-testid="clear-price-range"]');
        if (clearBtn) {
            clearBtn.innerHTML = '<i class="fas fa-check"></i> Đã xóa';
            setTimeout(() => {
                clearBtn.innerHTML = '<i class="fas fa-times"></i> Xóa';
            }, 1000);
        }

        // Re-filter products but don't auto-hide message
        filterProductsClientSide();
    }    // Form submission handling with loading state
    // (Form submission validation is now handled above in the validatePriceRange section)// Price validation
    document.getElementById('price-min-input').addEventListener('blur', function () {
        validatePriceRange();
    });

    document.getElementById('price-max-input').addEventListener('blur', function () {
        validatePriceRange();
    });

    // Function to validate price range
    function validatePriceRange() {
        const priceMinInput = document.getElementById('price-min-input');
        const priceMaxInput = document.getElementById('price-max-input');
        const priceMin = parseFloat(priceMinInput.value);
        const priceMax = parseFloat(priceMaxInput.value);
        
        // Clear previous validation states
        priceMinInput.classList.remove('is-invalid');
        priceMaxInput.classList.remove('is-invalid');
        priceMinInput.setCustomValidity('');
        priceMaxInput.setCustomValidity('');
        
        // Remove existing error message
        const existingError = document.getElementById('price-validation-error');
        if (existingError) {
            existingError.remove();
        }

        if (priceMin && priceMax && priceMin > priceMax) {
            // Add validation classes
            priceMinInput.classList.add('is-invalid');
            priceMaxInput.classList.add('is-invalid');
            
            // Set custom validity messages
            priceMinInput.setCustomValidity('Giá tối thiểu không thể lớn hơn giá tối đa');
            priceMaxInput.setCustomValidity('Giá tối đa không thể nhỏ hơn giá tối thiểu');
            
            // Show error message
            showPriceValidationError('Giá tối thiểu không thể lớn hơn giá tối đa');
            
            return false;
        }
        
        return true;
    }
      // Function to show price validation error
    function showPriceValidationError(message) {
        const priceRangeSection = document.getElementById('price-range-section');
        const errorDiv = document.createElement('div');
        errorDiv.id = 'price-validation-error';
        errorDiv.className = 'alert alert-danger alert-sm mt-2 mb-0';
        errorDiv.setAttribute('data-testid', 'price-validation-error');
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle me-1"></i>
            <small>${message}</small>
        `;
        priceRangeSection.appendChild(errorDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }

    // Override form submission to validate price range
    document.getElementById('product-filter-form').addEventListener('submit', function (e) {
        if (!validatePriceRange()) {
            e.preventDefault();
            
            // Show feedback on submit button
            const submitBtn = document.getElementById('apply-filters-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Vui lòng sửa lỗi khoảng giá';
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-primary');
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.classList.remove('btn-warning');
                submitBtn.classList.add('btn-primary');
            }, 3000);
            
            return false;
        }
        
        // Show loading state if validation passes
        const submitBtn = document.getElementById('apply-filters-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tìm kiếm...';
        submitBtn.disabled = true;

        // Re-enable button after a delay in case of errors
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });

    // Add keyboard shortcut (Enter) for search input
    document.getElementById('search-product-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('product-filter-form').submit();
        }
    });

    // Real-time filtering on input change (optional - for better UX)
    document.getElementById('search-product-input').addEventListener('input', function () {
        // Debounce the filtering to avoid too many calls
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => {
            filterProductsClientSide();
        }, 300);
    });    document.getElementById('price-min-input').addEventListener('input', function () {
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => {
            // Only filter if price range is valid
            if (validatePriceRange()) {
                filterProductsClientSide();
            }
        }, 500);
    });

    document.getElementById('price-max-input').addEventListener('input', function () {
        clearTimeout(this.filterTimeout);
        this.filterTimeout = setTimeout(() => {
            // Only filter if price range is valid
            if (validatePriceRange()) {
                filterProductsClientSide();
            }
        }, 500);
    });// Functions to show/hide search results title
    function showSearchTitle() {
        const searchTitle = document.getElementById('search-results-title');
        if (searchTitle) {
            searchTitle.classList.remove('hidden');
        }
    }
    
    function hideSearchTitle() {
        const searchTitle = document.getElementById('search-results-title');
        if (searchTitle) {
            searchTitle.classList.add('hidden');
        }
    }
    
    function toggleSearchTitle() {
        const searchTitle = document.getElementById('search-results-title');
        if (searchTitle) {
            searchTitle.classList.toggle('hidden');
        }
    }
    
    // Function to show/hide title based on conditions
    function updateSearchTitleVisibility() {
        const searchTitle = document.getElementById('search-results-title');
        const productsGrid = document.getElementById('products-grid');
        const productCards = productsGrid.querySelectorAll('[data-testid*="product-card-"]');
        
        // Count visible products
        let visibleProducts = 0;
        productCards.forEach(card => {
            if (card.style.display !== 'none' && !card.classList.contains('d-none')) {
                visibleProducts++;
            }
        });
        
        // Show title if there are products or if it's the initial load
        if (visibleProducts > 0 || !window.isClientSideFiltering) {
            showSearchTitle();
        } else {
            // You can choose to hide or keep showing the title when no products
            // For now, let's keep showing it
            showSearchTitle();
        }
    }    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Show title on initial load
        showSearchTitle();
        checkProductsVisibility();
    });
    
    // Expose functions to global scope for external access
    window.searchTitleControls = {
        show: showSearchTitle,
        hide: hideSearchTitle,
        toggle: toggleSearchTitle,
        update: updateSearchTitleVisibility
    };
</script>

<style>    /* Hidden class for show/hide functionality */
    .hidden {
        display: none !important;
    }
    
    /* Extra small button size */
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.25rem;
    }
    
    /* Adjust button spacing in product cards */
    .product-card .btn-xs {
        min-width: 80px;
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
      /* Custom styles for search filters */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Price validation error styling */
    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .alert-sm small {
        font-size: 0.8rem;
        margin: 0;
    }
    
    /* Price error alert styling */
    #price-error-alert {
        border-left: 4px solid #ffc107;
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }
    
    #price-error-alert strong {
        color: #856404;
    }
    
    /* Animation for validation error */
    #price-validation-error {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Loading state for buttons */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Price range info styling */
    #price-range-info {
        font-size: 0.8em;
        line-height: 1.3;
    }

    /* Filter buttons styling */
    #filter-buttons .btn {
        font-weight: 500;
        padding: 0.5rem 1rem;
    }

    #filter-buttons .btn i {
        margin-right: 0.5rem;
    }

    /* No products message styling */
    #no-products-filter-message {
        animation: fadeIn 0.5s ease-in-out;
    }

    #no-products-filter-message .alert {
        border: 2px dashed #ffc107;
        background-color: #fff3cd;
        border-radius: 15px;
        padding: 2rem;
    }

    #no-products-filter-message .fa-search {
        color: #6c757d;
        opacity: 0.7;
    }

    #no-products-filter-message h4 {
        color: #856404;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    #no-products-filter-message p {
        color: #664d03;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    #no-products-filter-message .btn {
        min-width: 180px;
        font-weight: 500;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    #no-products-filter-message .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #no-products-filter-message .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    /* Fade in animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }    /* Responsive adjustments */
    @media (max-width: 768px) {
        #filter-buttons .btn {
            font-size: 0.9rem;
        }

        #no-products-filter-message .alert {
            padding: 1.5rem;
        }

        #no-products-filter-message .btn {
            min-width: 150px;
            font-size: 0.9rem;
        }

        #no-products-filter-message .fa-search {
            font-size: 2rem !important;
        }
        
        /* Slightly larger buttons on mobile for better touch */
        .product-card .btn-xs {
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
            min-width: 70px;
        }
    }

    @media (max-width: 576px) {
        #no-products-filter-message .d-flex {
            flex-direction: column !important;
        }

        #no-products-filter-message .btn {
            min-width: 100%;
            margin-bottom: 0.5rem;
        }
        
        /* Stack buttons vertically on very small screens */
        .product-card .d-flex {
            flex-direction: column !important;
            gap: 0.5rem;
        }
        
        .product-card .btn-xs {
            width: 100%;
            min-width: unset;
        }
    }
</style>

<?php include "includes/footer.php"; ?>