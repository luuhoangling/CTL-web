<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Require login
requireLogin();

$user = getCurrentUser($conn);
$error = "";
$success = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Server-side validation (complementing JavaScript validation)
    // Only check what JavaScript cannot check (database-related validations)
    
    $userId = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $full_name);
    $email = mysqli_real_escape_string($conn, $email);
    
    // Check if email is already used by another user
    $checkEmailSql = "SELECT id FROM users WHERE email = '$email' AND id != $userId";
    $checkEmailResult = mysqli_query($conn, $checkEmailSql);
    
    if (mysqli_num_rows($checkEmailResult) > 0) {
        $error = "Email này đã được sử dụng bởi tài khoản khác.";
    } else {
        // If changing password, verify current password
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            if ($current_password !== $user['password']) {
                $error = "Mật khẩu hiện tại không đúng.";
            } else {
                // Update with new password (no hashing)
                $new_password = mysqli_real_escape_string($conn, $new_password);
                $updateSql = "UPDATE users SET full_name = '$full_name', email = '$email', password = '$new_password' WHERE id = $userId";
            }
        } else {
            // Update without changing password
            $updateSql = "UPDATE users SET full_name = '$full_name', email = '$email' WHERE id = $userId";
        }
        
        if (empty($error) && mysqli_query($conn, $updateSql)) {
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            $success = "Cập nhật thông tin thành công!";
            $user = getCurrentUser($conn); // Refresh user data
        } elseif (empty($error)) {
            $error = "Có lỗi xảy ra khi cập nhật thông tin.";
        }
    }
}

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row">        <div class="col-md-8 mx-auto">
            <div class="card profile-form-wrapper">
                <div class="card-header">
                    <h3>Thông Tin Cá Nhân</h3>
                </div>
                <div class="card-body">                    <div class="validation-summary" id="validationSummary" data-testid="validation-summary">
                        <h6>Vui lòng kiểm tra lại thông tin:</h6>
                        <ul id="validationErrors" data-testid="validation-errors"></ul>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" data-testid="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" data-testid="success-message"><?php echo $success; ?></div>
                    <?php endif; ?>
                      <form method="POST" action="" data-testid="profile-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" data-testid="username-field" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                                </div>
                            </div>
                            <div class="col-md-6">                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" data-testid="fullname-field"
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>
                            </div>
                        </div>                          <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" data-testid="email-field"
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        
                        <hr>
                        <h5>Thay Đổi Mật Khẩu</h5>
                        <p class="text-muted">Để lại trống nếu không muốn thay đổi mật khẩu.</p>
                          <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" data-testid="current-password-field">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" data-testid="new-password-field">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" data-testid="confirm-password-field">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary" data-testid="cancel-button">Hủy</a>
                            <button type="submit" class="btn btn-primary" data-testid="submit-button">Cập Nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
