<?php
// Shopping Cart Page
require_once "includes/config.php";
require_once "includes/functions.php";

// Require login for cart access
requireLogin();

include "includes/header.php";

// Get cart items
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartTotal = getCartTotal();
?>

<div class="container my-4">
    <h1 class="mb-4">Giỏ Hàng</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (count($cartItems) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th>Giá</th>
                        <th>Số Lượng</th>
                        <th>Tổng Phụ</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-3" style="width: 80px;" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                                    <a href="product.php?id=<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a>
                                </div>
                            </td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td>
                                <form action="cart_actions.php" method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10" class="form-control form-control-sm" style="width: 70px;">                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cập Nhật</button>
                                </form>
                            </td>
                            <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            <td>
                                <form action="cart_actions.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Tổng Cộng:</strong></td>
                        <td class="cart-total"><?php echo formatPrice($cartTotal); ?></td>
                        <td>
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Xóa Giỏ Hàng</button>
                            </form>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="products.php" class="btn btn-outline-primary">Tiếp Tục Mua Sắm</a>
            <a href="checkout.php" class="btn btn-success">Tiến Hành Thanh Toán</a>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            Giỏ hàng của bạn đang trống.
        </div>
        <a href="products.php" class="btn btn-primary">Xem Sản Phẩm</a>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
