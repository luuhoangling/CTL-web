<?php
// Admin Add Product
require_once "../../includes/config.php";

// Include the admin header
include "../includes/header.php";

// Initialize variables
$name = $description = $price = $category = $stock = $image_url = "";
$name_err = $description_err = $price_err = $category_err = $stock_err = $image_url_err = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Vui lòng nhập tên sản phẩm.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Vui lòng nhập mô tả sản phẩm.";
    } else {
        $description = trim($_POST["description"]);
    }
    
    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Vui lòng nhập giá sản phẩm.";
    } elseif (!is_numeric($_POST["price"]) || $_POST["price"] <= 0) {
        $price_err = "Vui lòng nhập giá trị hợp lệ lớn hơn 0.";
    } else {
        $price = trim($_POST["price"]);
    }
    
    // Validate category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Vui lòng nhập danh mục sản phẩm.";
    } else {
        $category = trim($_POST["category"]);
    }
    
    // Validate stock
    if (empty(trim($_POST["stock"]))) {
        $stock_err = "Vui lòng nhập số lượng tồn kho.";
    } elseif (!ctype_digit($_POST["stock"]) || $_POST["stock"] < 0) {
        $stock_err = "Vui lòng nhập số lượng hợp lệ không âm.";
    } else {
        $stock = trim($_POST["stock"]);
    }
      // Validate image URL
    if (empty(trim($_POST["image_url"]))) {
        $image_url_err = "Vui lòng nhập đường link hình ảnh.";
    } elseif (!filter_var($_POST["image_url"], FILTER_VALIDATE_URL)) {
        $image_url_err = "Vui lòng nhập đường link hợp lệ.";
    } else {
        $image_url = trim($_POST["image_url"]);
    }
      // Check input errors before inserting in database
    if (empty($name_err) && empty($description_err) && empty($price_err) && 
        empty($category_err) && empty($stock_err) && empty($image_url_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, description, price, image, category, stock) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssdssi", $param_name, $param_description, $param_price, $param_image, $param_category, $param_stock);
            
            // Set parameters            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_image = $image_url;
            $param_category = $category;
            $param_stock = $stock;
              // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Set success message and redirect
                $_SESSION['message'] = "Thêm sản phẩm thành công!";
                $_SESSION['message_type'] = "success";
                header("location: index.php");
                exit;
            } else {
                $_SESSION['message'] = "Đã xảy ra lỗi. Vui lòng thử lại sau.";
                $_SESSION['message_type'] = "danger";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="container-fluid">    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Thêm Sản Phẩm Mới</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay Lại Danh Sách
        </a>
    </div>
    
    <div class="card">        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Sản Phẩm *</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <div class="invalid-feedback"><?php echo $name_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá (đ) *</label>
                            <input type="number" name="price" step="0.01" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                            <div class="invalid-feedback"><?php echo $price_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh Mục *</label>
                            <input type="text" name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $category; ?>">
                            <div class="invalid-feedback"><?php echo $category_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock" class="form-label">Tồn Kho *</label>
                            <input type="number" name="stock" class="form-control <?php echo (!empty($stock_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $stock; ?>">
                            <div class="invalid-feedback"><?php echo $stock_err; ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả *</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $description; ?></textarea>
                            <div class="invalid-feedback"><?php echo $description_err; ?></div>
                        </div>
                          <div class="mb-3">
                            <label for="image_url" class="form-label">Đường Link Hình Ảnh *</label>
                            <input type="url" name="image_url" class="form-control <?php echo (!empty($image_url_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $image_url; ?>" placeholder="https://example.com/image.jpg">
                            <div class="invalid-feedback"><?php echo $image_url_err; ?></div>
                            <div class="form-text">Nhập đường link trực tiếp đến hình ảnh sản phẩm.</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Sản Phẩm
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include the admin footer
include "../includes/footer.php";
?>
