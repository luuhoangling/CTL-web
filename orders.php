<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Require login
requireLogin();

$userId = $_SESSION['user_id'];

// Get user information
$userSql = "SELECT * FROM users WHERE id = $userId";
$userResult = mysqli_query($conn, $userSql);
$userData = mysqli_fetch_assoc($userResult);
$userEmail = isset($userData['email']) ? $userData['email'] : '';

// If user email is not set, display all orders based on session user_id
// This is a fallback for older accounts or test accounts without email
if (empty($userEmail)) {
    // For demo purposes, just query all orders
    $displayAll = true;
} else {
    $displayAll = false;
}

// Get user's orders based on user_id or fall back to email
if ($displayAll) {
    // For demo purposes, show all orders
    $ordersSql = "SELECT o.*, 
                COUNT(oi.id) as item_count,
                GROUP_CONCAT(CONCAT(IFNULL(p.name, 'Unknown Product'), ' (x', oi.quantity, ')') SEPARATOR ', ') as items
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                LEFT JOIN products p ON oi.product_id = p.id 
                GROUP BY o.id 
                ORDER BY o.order_date DESC 
                LIMIT 10"; // Limiting to 10 most recent orders
} else {
    // Normal mode - show only user's orders using user_id or email as fallback
    $ordersSql = "SELECT o.*, 
                COUNT(oi.id) as item_count,
                GROUP_CONCAT(CONCAT(IFNULL(p.name, 'Unknown Product'), ' (x', oi.quantity, ')') SEPARATOR ', ') as items
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE o.user_id = $userId OR o.customer_email = '$userEmail'
                GROUP BY o.id 
                ORDER BY o.order_date DESC";
}

// Debug info - for development only
$debug = false; // Set to true to display debugging info
if ($debug) {
    echo "<pre>";
    echo "User ID: " . $userId . "<br>";
    echo "User Email: " . $userEmail . "<br>";
    echo "SQL Query: " . $ordersSql . "<br>";
    echo "</pre>";
}

$ordersResult = mysqli_query($conn, $ordersSql);
$orders = [];
if ($ordersResult && mysqli_num_rows($ordersResult) > 0) {
    while ($row = mysqli_fetch_assoc($ordersResult)) {
        $orders[] = $row;
    }
}

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Đơn Hàng Của Tôi</h2>
                <div>
                    <?php if (isset($userData['isAdmin']) && $userData['isAdmin'] == 1): ?>
                        <a href="debug_query.php?action=check_orders" class="btn btn-outline-secondary me-2">Debug
                            Orders</a>
                    <?php endif; ?>
                    <a href="products.php" class="btn btn-primary">Tiếp Tục Mua Hàng</a>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                    <h4 class="mt-3">Chưa có đơn hàng nào</h4>
                    <p class="text-muted">Bạn chưa có đơn hàng nào. Hãy khám phá các sản phẩm của chúng tôi!</p>
                    <a href="products.php" class="btn btn-primary">Xem Sản Phẩm</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($orders as $order): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div> <strong>Đơn hàng #<?php echo $order['id']; ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                        <br>
                                        <span class="badge bg-<?php
                                        echo match ($order['payment_status'] ?? 'pending') {
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'info',
                                            default => 'secondary'
                                        };
                                        ?>">
                                            <?php
                                            echo match ($order['payment_status'] ?? 'pending') {
                                                'pending' => 'Chờ thanh toán',
                                                'paid' => 'Đã thanh toán',
                                                'failed' => 'Thanh toán thất bại',
                                                'refunded' => 'Đã hoàn tiền',
                                                default => 'Chờ thanh toán'
                                            };
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Thông tin giao hàng:</strong><br>
                                        <small>
                                            <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                            <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                            <?php echo htmlspecialchars($order['customer_address']); ?>
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Sản phẩm:</strong><br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($order['items'] ?? 'Không có thông tin sản phẩm'); ?>
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary">
                                                Tổng: <?php echo formatPrice($order['total_amount']); ?>
                                            </strong>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                                Chi tiết
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Detail Modal -->
                        <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chi tiết đơn hàng #<?php echo $order['id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Thông tin khách hàng</h6>
                                                <p>
                                                    <strong>Họ tên:</strong>
                                                    <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                                    <strong>Email:</strong>
                                                    <?php echo htmlspecialchars($order['customer_email']); ?><br>
                                                    <strong>Điện thoại:</strong>
                                                    <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                                    <strong>Địa chỉ:</strong>
                                                    <?php echo htmlspecialchars($order['customer_address']); ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Thông tin đơn hàng</h6>
                                                <p>
                                                    <strong>Ngày đặt:</strong>
                                                    <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?><br>
                                                    <strong>Mã người dùng:</strong>
                                                    <?php echo $order['user_id'] ? '#' . $order['user_id'] : 'N/A'; ?><br>
                                                    <strong>Trạng thái đơn hàng:</strong> <span class="badge bg-warning">Chờ xử
                                                        lý</span><br>
                                                    <strong>Thanh toán:</strong>
                                                    <span class="badge bg-<?php
                                                    echo match ($order['payment_status'] ?? 'pending') {
                                                        'pending' => 'warning',
                                                        'paid' => 'success',
                                                        'failed' => 'danger',
                                                        'refunded' => 'info',
                                                        default => 'secondary'
                                                    };
                                                    ?>">
                                                        <?php
                                                        echo match ($order['payment_status'] ?? 'pending') {
                                                            'pending' => 'Chờ thanh toán',
                                                            'paid' => 'Đã thanh toán',
                                                            'failed' => 'Thanh toán thất bại',
                                                            'refunded' => 'Đã hoàn tiền',
                                                            default => 'Chờ thanh toán'
                                                        };
                                                        ?>
                                                    </span><br>
                                                    <strong>Phương thức:</strong> <?php echo $order['payment_method']; ?><br>
                                                    <?php if ($order['transaction_id']): ?>
                                                        <strong>Mã giao dịch:</strong>
                                                        <code><?php echo $order['transaction_id']; ?></code><br>
                                                    <?php endif; ?>
                                                    <strong>Tổng tiền:</strong> <span
                                                        class="text-primary"><?php echo formatPrice($order['total_amount']); ?></span>
                                                </p>
                                            </div>
                                        </div>

                                        <h6>Sản phẩm đã đặt</h6>
                                        <div class="table-responsive">
                                            <?php
                                            // Get order items for this specific order
                                            $orderItemsSql = "SELECT oi.*, p.name, p.image 
                                                            FROM order_items oi 
                                                            LEFT JOIN products p ON oi.product_id = p.id 
                                                            WHERE oi.order_id = " . $order['id'];
                                            $orderItemsResult = mysqli_query($conn, $orderItemsSql);
                                            ?>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Sản phẩm</th>
                                                        <th>Số lượng</th>
                                                        <th>Giá</th>
                                                        <th>Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($orderItemsResult && mysqli_num_rows($orderItemsResult) > 0):
                                                        while ($item = mysqli_fetch_assoc($orderItemsResult)):
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <img src="<?php echo $item['image'] ?? 'https://via.placeholder.com/40x40?text=No+Image'; ?>"
                                                                            alt="<?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?>"
                                                                            class="me-2"
                                                                            style="width: 40px; height: 40px; object-fit: cover;"
                                                                            onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                                                                        <?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?>
                                                                    </div>
                                                                </td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td><?php echo formatPrice($item['price']); ?></td>
                                                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                            </tr>
                                                            <?php
                                                        endwhile;
                                                    else:
                                                        ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center">Không có thông tin sản phẩm</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>