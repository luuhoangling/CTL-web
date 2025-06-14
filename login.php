<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Generate CSRF token
$csrf_token = generateCSRFToken();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = "Token bảo mật không hợp lệ. Vui lòng thử lại.";
        logSecurityEvent('CSRF_TOKEN_INVALID', 'Login attempt with invalid CSRF token');
    } else {
        // Get and validate input
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Server-side validation
        $validation_errors = [];
        
        if (empty($username)) {
            $validation_errors[] = "Tên đăng nhập không được để trống.";
        } elseif (!isValidUsername($username)) {
            $validation_errors[] = "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (3-50 ký tự).";
        }
        
        if (empty($password)) {
            $validation_errors[] = "Mật khẩu không được để trống.";
        } elseif (!isValidPassword($password)) {
            $validation_errors[] = "Mật khẩu phải có từ 6-255 ký tự.";
        }
        
        if (!empty($validation_errors)) {
            $error = implode("<br>", $validation_errors);
        } else {
            // Attempt login
            $loginResult = loginUser($conn, $username, $password);
            
            if ($loginResult['success']) {
                logSecurityEvent('LOGIN_SUCCESS', "User: $username");
                
                // Redirect based on user role
                if ($loginResult['isAdmin'] == 1) { 
                    // Admin user
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $_SESSION['user_id'];
                    $_SESSION['admin_username'] = $_SESSION['username'];
                    $_SESSION['admin_full_name'] = $_SESSION['full_name'];
                    
                    // Check for redirect parameter
                    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'admin/index.php';
                    unset($_SESSION['redirect_after_login']);
                    
                    header("Location: " . $redirect);
                } else { 
                    // Regular user
                    // Check for redirect parameter
                    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                    unset($_SESSION['redirect_after_login']);
                    
                    header("Location: " . $redirect);
                }
                exit();
            } else {
                $error = $loginResult['error'];
                logSecurityEvent('LOGIN_FAILED', "User: $username | Error: " . $loginResult['error']);
            }
        }
    }
}

include "includes/header.php";
?>

<style>
.hidden {
    display: none !important;
}
.error-message {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Đăng Nhập</h3>
                </div>                <div class="card-body">                    <!-- Server-side errors -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert" id="server-error">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert" id="server-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-warning" role="alert" id="session-error">
                            <?php 
                            echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); 
                            unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Client-side login fail error (hidden by default) -->
                    <div class="alert alert-danger hidden" role="alert" id="login-fail">
                        Tài khoản hoặc mật khẩu không chính xác
                    </div>
                      
                    <form method="POST" action="" id="loginForm" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                          
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                            
                            <!-- Username errors (all hidden by default) -->
                            <div class="error-message hidden" id="username-empty">
                                Tên tài khoản không được để trống
                            </div>
                            <div class="error-message hidden" id="username-invalid">
                                Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (3-50 ký tự)
                            </div>
                            <div class="error-message hidden" id="username-error">
                                Tên đăng nhập không hợp lệ
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password"
                                   maxlength="255">
                            
                            <!-- Password errors (all hidden by default) -->
                            <div class="error-message hidden" id="password-empty">
                                Mật khẩu không được để trống
                            </div>
                            <div class="error-message hidden" id="password-short">
                                Mật khẩu phải có ít nhất 6 ký tự
                            </div>
                            <div class="error-message hidden" id="password-error">
                                Mật khẩu không hợp lệ
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                                <label class="form-check-label" for="remember_me">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Đăng Nhập
                            </button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    
    // Get all error message elements
    const usernameEmpty = document.getElementById('username-empty');
    const usernameInvalid = document.getElementById('username-invalid');
    const usernameError = document.getElementById('username-error');
    const passwordEmpty = document.getElementById('password-empty');
    const passwordShort = document.getElementById('password-short');
    const passwordError = document.getElementById('password-error');
    const loginFail = document.getElementById('login-fail');
    
    // Username pattern for validation
    const usernamePattern = /^[a-zA-Z0-9_]{3,50}$/;
    
    // Helper functions
    function hideError(errorElement) {
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }
    
    function showError(errorElement) {
        if (errorElement) {
            errorElement.classList.remove('hidden');
        }
    }
    
    function hideAllUsernameErrors() {
        hideError(usernameEmpty);
        hideError(usernameInvalid);
        hideError(usernameError);
    }
    
    function hideAllPasswordErrors() {
        hideError(passwordEmpty);
        hideError(passwordShort);
        hideError(passwordError);
    }
    
    function hideAllErrors() {
        hideAllUsernameErrors();
        hideAllPasswordErrors();
        hideError(loginFail);
    }
    
    // Remove input validation styling
    function removeInputStyling(input) {
        input.classList.remove('is-invalid', 'is-valid');
    }
    
    function addInvalidStyling(input) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
    }
    
    function addValidStyling(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }
    
    // Validation functions
    function validateUsername() {
        const username = usernameInput.value.trim();
        
        hideAllUsernameErrors();
        
        if (username === '') {
            showError(usernameEmpty);
            addInvalidStyling(usernameInput);
            return false;
        } else if (!usernamePattern.test(username)) {
            showError(usernameInvalid);
            addInvalidStyling(usernameInput);
            return false;
        } else {
            addValidStyling(usernameInput);
            return true;
        }
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        
        hideAllPasswordErrors();
        
        if (password === '') {
            showError(passwordEmpty);
            addInvalidStyling(passwordInput);
            return false;
        } else if (password.length < 6) {
            showError(passwordShort);
            addInvalidStyling(passwordInput);
            return false;
        } else {
            addValidStyling(passwordInput);
            return true;
        }
    }
    
    // Event listeners for hiding errors when user starts typing
    usernameInput.addEventListener('input', function() {
        hideAllUsernameErrors();
        hideError(loginFail);
        
        // Remove styling when user starts typing
        if (usernameInput.value.trim() !== '') {
            removeInputStyling(usernameInput);
        }
    });
    
    passwordInput.addEventListener('input', function() {
        hideAllPasswordErrors();
        hideError(loginFail);
        
        // Remove styling when user starts typing
        if (passwordInput.value !== '') {
            removeInputStyling(passwordInput);
        }
    });
    
    // Form submission
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Hide all previous errors
        hideAllErrors();
        
        // Validate form
        const isUsernameValid = validateUsername();
        const isPasswordValid = validatePassword();
        
        if (isUsernameValid && isPasswordValid) {
            // Show loading state
            loginBtn.disabled = true;
            const originalText = loginBtn.innerHTML;
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang đăng nhập...';
            
            // Submit form (with delay to show loading state)
            setTimeout(function() {
                loginForm.submit();
            }, 300);
            
            // Reset button if form doesn't submit (fallback)
            setTimeout(function() {
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalText;
            }, 10000);
        } else {
            // Focus on first invalid field
            if (!isUsernameValid) {
                usernameInput.focus();
            } else if (!isPasswordValid) {
                passwordInput.focus();
            }
        }
    });
    
    // Prevent double submission
    let isSubmitting = false;
    loginForm.addEventListener('submit', function(event) {
        if (isSubmitting) {
            event.preventDefault();
            return false;
        }
        isSubmitting = true;
    });
});
</script>

<?php include "includes/footer.php"; ?>
