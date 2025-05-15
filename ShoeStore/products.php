<?php
// Products listing page
require_once "includes/config.php";
require_once "includes/functions.php";
include "includes/header.php";

// Get all products
$products = getAllProducts($conn);
?>

<div class="container my-4">
    <h1 class="mb-4">All Products</h1>
    
    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <img src="assets/images/<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text small text-muted"><?php echo substr($product['description'], 0, 60); ?>...</p>
                        <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                        <div class="d-flex justify-content-between">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-success">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No products found.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
