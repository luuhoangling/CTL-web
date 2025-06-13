<?php
// Checkout Page
require_once "includes/config.php";
require_once "includes/functions.php";

// Require login for checkout
requireLogin();

include "includes/header.php";

// Check if cart is empty
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartTotal = getCartTotal();

if (count($cartItems) == 0) {
    // Cart is empty, redirect to cart page
    header("Location: cart.php");
    exit;
}

// Process order
$errors = [];
$success = false;
$orderId = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'COD');
    $note = trim($_POST['note'] ?? '');
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    // If no errors, create order
    if (empty($errors)) {
        $customerData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'payment_method' => $payment_method,
            'note' => $note
        ];
        
        $orderId = createOrder($conn, $customerData);
        
        if ($orderId) {
            $success = true;
        } else {
            $errors[] = "An error occurred while processing your order. Please try again.";
        }
    }
}
?>

<div class="container my-4">
    <h1 class="mb-4">Thanh Toán</h1>
    
    <?php if (!$success): ?>
        <div class="row">
            <div class="col-md-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông Tin Giao Hàng</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="checkout.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ Tên*</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa Chỉ Email*</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số Điện Thoại*</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                              <div class="mb-3">
                                <label for="address" class="form-label">Địa Chỉ Giao Hàng*</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Phương Thức Thanh Toán</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="COD" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'COD') ? 'selected' : ''; ?>>Thanh toán khi nhận hàng (COD)</option>
                                    <option value="BankTransfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'BankTransfer') ? 'selected' : ''; ?>>Chuyển khoản ngân hàng</option>
                                    <option value="Momo" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Momo') ? 'selected' : ''; ?>>Ví MoMo</option>
                                    <option value="ZaloPay" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'ZaloPay') ? 'selected' : ''; ?>>ZaloPay</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi Chú</label>
                                <textarea class="form-control" id="note" name="note" rows="2"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
                                <div class="form-text">Ghi chú về đơn hàng, ví dụ: thời gian hay địa điểm giao hàng chi tiết.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Đặt Hàng</button>
                            <a href="cart.php" class="btn btn-outline-secondary">Quay Lại Giỏ Hàng</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tóm Tắt Đơn Hàng</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php echo $item['name']; ?> 
                                        <span class="text-muted">x <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                <div>Tổng Cộng</div>
                                <span><?php echo formatPrice($cartTotal); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
          <?php else: ?>
        <?php
        // Get order details
        $orderSql = "SELECT * FROM orders WHERE id = $orderId";
        $orderResult = mysqli_query($conn, $orderSql);
        $orderData = mysqli_fetch_assoc($orderResult);
        
        $itemsSql = "SELECT oi.*, p.name, p.image FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = $orderId";
        $itemsResult = mysqli_query($conn, $itemsSql);
        ?>
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h2 class="mt-3">Đặt Hàng Thành Công!</h2>
                <p class="lead">Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#<?php echo $orderId; ?></strong></p>
                <p>Chúng tôi sẽ xử lý đơn hàng của bạn sớm và thông báo khi đơn hàng được gửi đi.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Thông Tin Đơn Hàng</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>Mã đơn hàng:</th>
                                <td>#<?php echo $orderId; ?></td>
                            </tr>
                            <tr>
                                <th>Ngày đặt:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($orderData['order_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Tổng tiền:</th>
                                <td class="fw-bold"><?php echo formatPrice($orderData['total_amount']); ?></td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán:</th>
                                <td><?php echo $orderData['payment_method']; ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái thanh toán:</th>
                                <td>
                                    <?php if($orderData['payment_method'] == 'COD'): ?>
                                        <span class="badge bg-warning">Thanh toán khi nhận hàng</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Chờ thanh toán</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if($orderData['transaction_id']): ?>
                            <tr>
                                <th>Mã giao dịch:</th>
                                <td><code><?php echo $orderData['transaction_id']; ?></code></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Thông Tin Giao Hàng</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Tên:</strong> <?php echo $orderData['customer_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $orderData['customer_email']; ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo $orderData['customer_phone']; ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo $orderData['customer_address']; ?></p>
                        <?php if($orderData['note']): ?>
                        <p><strong>Ghi chú:</strong> <?php echo $orderData['note']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Sản Phẩm Đã Đặt</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($itemsResult)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($item['image']): ?>
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div><?php echo $item['name']; ?></div>
                                </div>
                            </td>
                            <td class="text-end"><?php echo formatPrice($item['price']); ?></td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Tổng cộng:</th>
                            <th class="text-end"><?php echo formatPrice($orderData['total_amount']); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <div class="text-center mb-4">
            <a href="orders.php" class="btn btn-primary">Xem Đơn Hàng Của Bạn</a>
            <a href="index.php" class="btn btn-outline-secondary ms-2">Quay Về Trang Chủ</a>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
