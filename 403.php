<?php
// 403 Forbidden Error Page
include "includes/header.php";
?>

<div class="container py-5 text-center">
    <div class="py-5">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <h2 class="mb-4">Truy Cập Bị Từ Chối</h2>
        <p class="lead mb-4">Bạn không có quyền truy cập tài nguyên này.</p>
        <p>Nếu bạn đang gặp lỗi này, vui lòng thực hiện các bước sau:</p>
        <ul class="list-group list-group-flush mb-4 w-75 mx-auto text-start">
            <li class="list-group-item">Kiểm tra quyền truy cập thư mục của XAMPP/Apache</li>
            <li class="list-group-item">Đảm bảo thiết lập đúng quyền cho thư mục project</li>
            <li class="list-group-item">Kiểm tra cấu hình Apache trong file httpd.conf</li>
            <li class="list-group-item">Thử khởi động lại dịch vụ Apache</li>
        </ul>
        <a href="index.php" class="btn btn-primary">Quay lại Trang Chủ</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>
