<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập tên đăng nhập và mật khẩu.";
    } else {
        $loginResult = loginUser($conn, $username, $password);
          if ($loginResult !== false) {
            // Chuyển hướng dựa trên isAdmin
            if ($loginResult == 1) { 
                // isAdmin = 1 -> set admin session và đến admin
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $_SESSION['user_id'];
                $_SESSION['admin_username'] = $_SESSION['username'];
                $_SESSION['admin_full_name'] = $_SESSION['full_name'];
                
                header("Location: admin/index.php");
            } else { 
                // isAdmin = 0 -> đến trang chủ
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
        }
    }
}

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Đăng Nhập</h3>
                </div>                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-warning">
                            <?php 
                            echo $_SESSION['error_message']; 
                            unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php endif; ?>
                      <form method="POST" action="" id="loginForm" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            <div id="username-error" style="color: #dc3545; display: none; font-size: 0.875em; margin-top: 0.25rem;">
                                Tên đăng nhập không được bỏ trống.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div id="password-error" style="color: #dc3545; display: none; font-size: 0.875em; margin-top: 0.25rem;">
                                Mật khẩu không được bỏ trống.
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    let isValid = true;
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');
    
    // Reset previous validation
    usernameError.style.display = 'none';
    passwordError.style.display = 'none';
    
    // Validate username
    if (username.value.trim() === '') {
        usernameError.style.display = 'block';
        isValid = false;
    }
    
    // Validate password
    if (password.value.trim() === '') {
        passwordError.style.display = 'block';
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
});
</script>

<?php include "includes/footer.php"; ?>
