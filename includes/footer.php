    </main>    
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5>Cửa Hàng CTL</h5>
                    <p class="mb-3">Cung cấp các mặt hàng thời trang đa dạng mới nhất từ 2024</p>
                    <div class="mb-4">
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white me-3 fs-5"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5>Liên Kết Nhanh</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white"><i class="bi bi-chevron-right me-2 text-primary"></i>Trang Chủ</a></li>
                        <li class="mb-2"><a href="products.php" class="text-white"><i class="bi bi-chevron-right me-2 text-primary"></i>Sản Phẩm</a></li>
                        <li class="mb-2"><a href="cart.php" class="text-white"><i class="bi bi-chevron-right me-2 text-primary"></i>Giỏ Hàng</a></li>
                        <li class="mb-2"><a href="categories.php" class="text-white"><i class="bi bi-chevron-right me-2 text-primary"></i>Danh Mục</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5>Liên Hệ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-2 text-primary"></i>
                            Số 141 Đường Chiến Thắng, Tân Triều, Thanh Trì, Hà Nội
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope me-2 text-primary"></i>
                            <a href="mailto:contact@ctl.com" class="text-white">contact@ctl.com</a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2 text-primary"></i>
                            <a href="tel:+84987654321" class="text-white">+84 987 654 321</a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock me-2 text-primary"></i>
                            8:00 - 22:00, Thứ Hai - Chủ Nhật
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="background-color: rgba(255,255,255,0.2);">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong class="text-primary">CTL Store</strong>. Bảo lưu mọi quyền.</p>
                </div>
            </div>
        </div>
    </footer><!-- Bootstrap JS and Popper.js -->    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <script>
        // Initialize AOS animation
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-in-out',
            disable: 'mobile'
        });
        
        // Scroll to top button
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
