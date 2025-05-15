<?php
// Admin Edit Product
require_once "../../includes/config.php";

// Include the admin header
include "../includes/header.php";

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid product ID.";
    $_SESSION['message_type'] = "danger";
    header("location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Initialize variables
$name = $description = $price = $category = $stock = $current_image = "";
$name_err = $description_err = $price_err = $category_err = $stock_err = $image_err = "";

// Fetch product data
$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $product = mysqli_fetch_assoc($result);
            $name = $product['name'];
            $description = $product['description'];
            $price = $product['price'];
            $category = $product['category'];
            $stock = $product['stock'];
            $current_image = $product['image'];
        } else {
            $_SESSION['message'] = "Product not found.";
            $_SESSION['message_type'] = "danger";
            header("location: index.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again later.";
        $_SESSION['message_type'] = "danger";
        header("location: index.php");
        exit;
    }
    
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = "Something went wrong. Please try again later.";
    $_SESSION['message_type'] = "danger";
    header("location: index.php");
    exit;
}

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
    
    // Handle image upload
    $image = $current_image;
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $image_err = "Please select a valid image format (JPG, PNG, GIF).";
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $image_err = "Image size must be less than 5MB.";
        }
        
        // Verify MIME type
        if (in_array($filetype, $allowed)) {
            // Generate unique filename
            $new_filename = uniqid() . "." . $ext;
            $upload_path = "../../assets/images/" . $new_filename;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                // Delete old image if it exists and not the default
                if (!empty($current_image) && file_exists("../../assets/images/" . $current_image)) {
                    unlink("../../assets/images/" . $current_image);
                }
                $image = $new_filename;
            } else {
                $image_err = "Error uploading the image.";
            }
        } else {
            $image_err = "Invalid file type.";
        }
    }
    
    // Check input errors before updating in database
    if (empty($name_err) && empty($description_err) && empty($price_err) && 
        empty($category_err) && empty($stock_err) && empty($image_err)) {
        
        // Prepare an update statement
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, image = ?, category = ?, stock = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssdssii", $param_name, $param_description, $param_price, $param_image, $param_category, $param_stock, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_description = $description;
            $param_price = $price;
            $param_image = $image;
            $param_category = $category;
            $param_stock = $stock;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Set success message and redirect
                $_SESSION['message'] = "Product updated successfully!";
                $_SESSION['message_type'] = "success";
                header("location: index.php");
                exit;
            } else {
                $_SESSION['message'] = "Something went wrong. Please try again later.";
                $_SESSION['message_type'] = "danger";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Product</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <div class="invalid-feedback"><?php echo $name_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Price (đ) *</label>
                            <input type="number" name="price" step="0.01" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                            <div class="invalid-feedback"><?php echo $price_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <input type="text" name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $category; ?>">
                            <div class="invalid-feedback"><?php echo $category_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control <?php echo (!empty($stock_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $stock; ?>">
                            <div class="invalid-feedback"><?php echo $stock_err; ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $description; ?></textarea>
                            <div class="invalid-feedback"><?php echo $description_err; ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <?php if (!empty($current_image)): ?>
                                <div class="mb-2">
                                    <img src="../../assets/images/<?php echo $current_image; ?>" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                                    <div><small class="text-muted">Current image</small></div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $image_err; ?></div>
                            <div class="form-text">Leave empty to keep current image. Supported formats: JPG, PNG, GIF. Max size: 5MB.</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Product
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include the admin footer
include "../includes/footer.php";
?>
