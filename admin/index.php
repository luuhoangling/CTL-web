<?php
// Admin Dashboard
require_once "../includes/config.php";

// Include the admin header
include "includes/header.php";

// Get stats
// Total products
$productQuery = "SELECT COUNT(*) as total_products FROM products";
$productResult = mysqli_query($conn, $productQuery);
$totalProducts = mysqli_fetch_assoc($productResult)['total_products'];

// Total orders
$orderQuery = "SELECT COUNT(*) as total_orders FROM orders";
$orderResult = mysqli_query($conn, $orderQuery);
$totalOrders = mysqli_fetch_assoc($orderResult)['total_orders'];

// Total revenue
$revenueQuery = "SELECT SUM(total_amount) as total_revenue FROM orders";
$revenueResult = mysqli_query($conn, $revenueQuery);
$totalRevenue = mysqli_fetch_assoc($revenueResult)['total_revenue'] ?? 0;

// Recent orders
$recentOrdersQuery = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
$recentOrdersResult = mysqli_query($conn, $recentOrdersQuery);
$recentOrders = [];
while ($row = mysqli_fetch_assoc($recentOrdersResult)) {
    $recentOrders[] = $row;
}
?>

<div class="container-fluid">    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bảng Điều Khiển</h1>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng Sản Phẩm</h5>
                            <h2 class="mb-0"><?php echo $totalProducts; ?></h2>
                        </div>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="products/index.php" class="text-white text-decoration-none">Xem Sản Phẩm</a>
                </div>
            </div>
        </div>
          <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng Đơn Hàng</h5>
                            <h2 class="mb-0"><?php echo $totalOrders; ?></h2>
                        </div>
                        <i class="bi bi-bag-check fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="text-white text-decoration-none">Xem Đơn Hàng</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Tổng Doanh Thu</h5>
                            <h2 class="mb-0"><?php echo number_format($totalRevenue, 0, ',', '.') . ' đ'; ?></h2>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="text-white text-decoration-none">Xem Doanh Số</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đơn Hàng Gần Đây</h5>
                </div>
                <div class="card-body">
                    <?php if (count($recentOrders) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Tiền</th>
                                        <th>Ngày Đặt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 0, ',', '.') . ' đ'; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>                            </table>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">Chưa có đơn hàng nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the admin footer
include "includes/footer.php";
?>
