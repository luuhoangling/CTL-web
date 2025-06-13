// Main JavaScript for CTL Store website

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (typeof bootstrap !== 'undefined') {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    if (typeof bootstrap !== 'undefined') {
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
    
    // Auto dismiss alerts after 3 seconds with fade-out effect
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 1s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                } else {
                    alert.remove();
                }
            }, 1000);
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
    
    // Add parallax effect to the header section
    const heroSection = document.querySelector('.py-5.bg-light');
    if (heroSection) {
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            if (scrollPosition < 600) {
                heroSection.style.backgroundPosition = `50% ${scrollPosition * 0.05}px`;
            }
        });
    }
    
    // Animated counter for numbers
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    // Animate counters when they come into view
    const counters = document.querySelectorAll('.counter-number');
    if (counters.length > 0) {
        const options = {
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target'));
                    animateValue(counter, 0, target, 2000);
                    observer.unobserve(counter);
                }
            });
        }, options);
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
    }
    
    // Lazy loading images with blur-up effect
    const lazyImages = document.querySelectorAll('.lazy-load');
    if (lazyImages.length > 0) {
        if ('IntersectionObserver' in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.add('loaded');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }
    }
    
    // Newsletter form submit handler
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email) {
                // Show success message
                emailInput.value = '';
                
                // Create and insert success message
                const successMessage = document.createElement('div');
                successMessage.className = 'alert alert-light mt-2 mb-0 py-2 small';
                successMessage.innerHTML = 'Cảm ơn bạn đã đăng ký!';
                
                // If a previous message exists, remove it
                const previousMessage = newsletterForm.querySelector('.alert');
                if (previousMessage) {
                    previousMessage.remove();
                }
                
                newsletterForm.appendChild(successMessage);
                
                // Remove message after 3 seconds
                setTimeout(() => {
                    successMessage.style.transition = 'opacity 0.5s ease';
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.remove();
                    }, 500);
                }, 3000);
            }
        });
    }
    
    // Add to cart animation
    const addToCartButtons = document.querySelectorAll('button[type="submit"]');
    addToCartButtons.forEach(button => {
        if (button.textContent.includes('Thêm Vào Giỏ') || button.innerHTML.includes('bi-cart-plus')) {
            button.addEventListener('click', function(e) {
                // Create an animated cart icon
                const cart = document.querySelector('.nav-link .bi-cart-fill');
                
                if (cart) {
                    // Prevent double animation
                    if (cart.classList.contains('cart-animation-active')) {
                        return;
                    }
                    
                    // Add animation class
                    cart.classList.add('cart-animation-active');
                    
                    // Remove class after animation completes
                    setTimeout(() => {
                        cart.classList.remove('cart-animation-active');
                    }, 1000);
                }
            });
        }
    });
});
