<?php
// Admin Products List
require_once "../../includes/config.php";

// Include the admin header
include "../includes/header.php";

// Get all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$products = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Check for success or error messages
$message = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="container-fluid">    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản Lý Sản Phẩm</h1>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus"></i> Thêm Sản Phẩm Mới
        </a>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <?php if (count($products) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình Ảnh</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Danh Mục</th>
                                <th>Giá</th>
                                <th>Tồn Kho</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="../../assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 50px; height: 50px; object-fit: contain;">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td><?php echo number_format($product['price'], 0, ',', '.') . ' đ'; ?></td>
                                    <td><?php echo $product['stock']; ?></td>                                    <td>
                                        <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Sửa
                                        </a>
                                        <a href="delete.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger delete-product">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>                </div>
            <?php else: ?>
                <p class="mb-0">Không tìm thấy sản phẩm nào. <a href="create.php">Thêm sản phẩm mới</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include the admin footer
include "../includes/footer.php";
?>
