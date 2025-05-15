<?php
// Checkout Page
require_once "includes/config.php";
require_once "includes/functions.php";
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
            'address' => $address
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
    <h1 class="mb-4">Checkout</h1>
    
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
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="checkout.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name*</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address*</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number*</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Shipping Address*</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Place Order</button>
                            <a href="cart.php" class="btn btn-outline-secondary">Return to Cart</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
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
                                <div>Total</div>
                                <span><?php echo formatPrice($cartTotal); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h2 class="mt-3">Order Placed Successfully!</h2>
                <p class="lead">Thank you for your order. Your order number is: <strong>#<?php echo $orderId; ?></strong></p>
                <p>We'll process your order soon and notify you when it ships.</p>
                <a href="index.php" class="btn btn-primary mt-3">Return to Homepage</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
