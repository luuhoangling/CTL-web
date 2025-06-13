<?php
// This file will contain functions to render category-related UI components.

// Example function (you can replace or extend this)
if (!function_exists('renderCategoryNavigation')) {
    function renderCategoryNavigation($categories, $currentCategoryId) {
        // Implementation for category navigation
        echo '<nav class="nav nav-pills flex-column flex-sm-row">';
        echo '<a class="flex-sm-fill text-sm-center nav-link" href="categories.php">Tất cả danh mục</a>';
        foreach ($categories as $category) {
            $activeClass = ($currentCategoryId == $category['id']) ? 'active' : '';
            echo '<a class="flex-sm-fill text-sm-center nav-link ' . $activeClass . '" href="categories.php?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('renderPriceRangeSlider')) {
    function renderPriceRangeSlider($minPrice, $maxPrice, $currentMin, $currentMax) {
        // Implementation for price range slider
        echo '<div class="mb-3">';
        echo '<h6>Khoảng giá</h6>';
        echo '<div class="d-flex">';
        echo '<input type="number" name="min_price" class="form-control form-control-sm me-2" placeholder="Từ" value="' . htmlspecialchars($currentMin) . '" min="' . $minPrice . '" max="' . $maxPrice . '">';
        echo '<input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="' . htmlspecialchars($currentMax) . '" min="' . $minPrice . '" max="' . $maxPrice . '">';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('renderAttributeFilters')) {
    function renderAttributeFilters($attributes, $selectedAttributes) {
        // Implementation for attribute filters
        if (!empty($attributes)) {
            echo '<div class="mb-3">';
            echo '<h6>Thuộc tính</h6>';
            foreach ($attributes as $attribute) {
                echo '<div class="mb-2">';
                echo '<strong>' . htmlspecialchars($attribute['name']) . '</strong>';
                foreach ($attribute['values'] as $value) {
                    $checked = isset($selectedAttributes[$attribute['id']]) && in_array($value, $selectedAttributes[$attribute['id']]) ? 'checked' : '';
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="checkbox" name="attr[' . $attribute['id'] . '][]" value="' . htmlspecialchars($value) . '" id="attr_' . $attribute['id'] . '_' . htmlspecialchars($value) . '" ' . $checked . '>';
                    echo '<label class="form-check-label" for="attr_' . $attribute['id'] . '_' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</label>';
                    echo '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

?>
