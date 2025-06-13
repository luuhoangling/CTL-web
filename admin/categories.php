<?php
// Admin Category Management
require_once "../../includes/config.php";
require_once "../../includes/category_functions.php";

// Include the admin header
include "../includes/header.php";

// Handle category operations
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                
                $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
                if (mysqli_query($conn, $sql)) {
                    $message = "Danh mục đã được thêm thành công!";
                    $message_type = "success";
                } else {
                    $message = "Lỗi: " . mysqli_error($conn);
                    $message_type = "danger";
                }
                break;
                
            case 'edit':
                $id = intval($_POST['id']);
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                
                $sql = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $id";
                if (mysqli_query($conn, $sql)) {
                    $message = "Danh mục đã được cập nhật thành công!";
                    $message_type = "success";
                } else {
                    $message = "Lỗi: " . mysqli_error($conn);
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                // Check if category has products
                $checkSql = "SELECT COUNT(*) as count FROM products WHERE category_id = $id";
                $checkResult = mysqli_query($conn, $checkSql);
                $count = mysqli_fetch_assoc($checkResult)['count'];
                
                if ($count > 0) {
                    $message = "Không thể xóa danh mục này vì vẫn còn sản phẩm trong danh mục!";
                    $message_type = "warning";
                } else {
                    $sql = "DELETE FROM categories WHERE id = $id";
                    if (mysqli_query($conn, $sql)) {
                        $message = "Danh mục đã được xóa thành công!";
                        $message_type = "success";
                    } else {
                        $message = "Lỗi: " . mysqli_error($conn);
                        $message_type = "danger";
                    }
                }
                break;
        }
    }
}

// Get all categories
$categories = getAllCategories($conn);

// Get category for editing
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $sql = "SELECT * FROM categories WHERE id = $editId";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $editCategory = mysqli_fetch_assoc($result);
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản Lý Danh Mục</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Add/Edit Category Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $editCategory ? 'Sửa Danh Mục' : 'Thêm Danh Mục Mới'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $editCategory ? 'edit' : 'add'; ?>">
                        <?php if ($editCategory): ?>
                            <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Danh Mục</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editCategory ? $editCategory['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $editCategory ? $editCategory['description'] : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editCategory ? 'Cập Nhật' : 'Thêm Mới'; ?>
                        </button>
                        
                        <?php if ($editCategory): ?>
                            <a href="categories.php" class="btn btn-secondary">Hủy</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Categories List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh Sách Danh Mục</h5>
                </div>
                <div class="card-body">
                    <?php if (count($categories) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Danh Mục</th>
                                        <th>Mô Tả</th>
                                        <th>Số Sản Phẩm</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <?php
                                        // Count products in category
                                        $countSql = "SELECT COUNT(*) as count FROM products WHERE category_id = " . $category['id'];
                                        $countResult = mysqli_query($conn, $countSql);
                                        $productCount = mysqli_fetch_assoc($countResult)['count'];
                                        ?>
                                        <tr>
                                            <td><?php echo $category['id']; ?></td>
                                            <td><?php echo $category['name']; ?></td>
                                            <td><?php echo $category['description'] ?: '-'; ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $productCount; ?></span>
                                            </td>
                                            <td>
                                                <a href="categories.php?edit=<?php echo $category['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <a href="attributes.php?category_id=<?php echo $category['id']; ?>" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-tags"></i>
                                                </a>
                                                
                                                <?php if ($productCount == 0): ?>
                                                    <form method="POST" action="" class="d-inline" 
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Chưa có danh mục nào. Hãy thêm danh mục đầu tiên!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
