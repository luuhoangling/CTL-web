<?php
error_reporting(error_level: E_ALL);
ini_set(option: 'display_errors', value: 1);
// Admin Login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: ../index.php");
    exit;
}

// Database connection
require_once "../../includes/config.php";
require_once "../../includes/functions.php";

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Vui lòng nhập tên đăng nhập.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Vui lòng nhập mật khẩu.";
    } else {
        $password = trim($_POST["password"]);
    }    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Sử dụng function loginUser từ functions.php
        require_once "../../includes/functions.php";
        
        $loginResult = loginUser($conn, $username, $password);
        
        if ($loginResult == 1) { // isAdmin = 1
            // Set admin session để tương thích với code admin hiện tại
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $_SESSION['user_id'];
            $_SESSION['admin_username'] = $_SESSION['username'];
            $_SESSION['admin_full_name'] = $_SESSION['full_name'];
            
            header("location: ../index.php");
            exit();
        } else {
            $login_err = "Tên đăng nhập hoặc mật khẩu không đúng hoặc bạn không có quyền admin.";
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - Cửa Hàng Giày</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">Đăng Nhập Quản Trị</h4>
            </div>
            <div class="card-body p-4">
                <?php
                if (!empty($login_err)) {
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }
                ?>                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username"
                            class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" name="password"
                            class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                    </div>                </form>
            </div>
            <div class="card-footer text-center py-3">
                <a href="../../index.php" class="text-decoration-none">Quay Lại Trang Web</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>