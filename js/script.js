// Main JavaScript for the shoe store website

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Auto dismiss alerts after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-success');
        alerts.forEach(function(alert) {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        });
    }, 3000);
    
    // Quantity increment/decrement buttons in product page
    const quantitySelect = document.getElementById('quantity');
    if (quantitySelect) {
        document.querySelector('.quantity-decrease')?.addEventListener('click', function() {
            if (quantitySelect.value > 1) {
                quantitySelect.value = parseInt(quantitySelect.value) - 1;
            }
        });
        
        document.querySelector('.quantity-increase')?.addEventListener('click', function() {
            if (quantitySelect.value < parseInt(quantitySelect.max)) {
                quantitySelect.value = parseInt(quantitySelect.value) + 1;
            }
        });
    }
    
    // Delete confirmation for admin product deletion
    const deleteButtons = document.querySelectorAll('.delete-product');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this product?')) {
                e.preventDefault();
            }
        });
    });
});
