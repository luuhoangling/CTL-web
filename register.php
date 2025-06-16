<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Initialize error states for each field
$errors = [
    'full_name' => '',
    'username' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'general' => ''
];

$success = "";
$form_submitted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_submitted = true;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    
    // Individual field validation
    if (empty($full_name)) {
        $errors['full_name'] = "Vui lòng nhập họ và tên.";
    } elseif (strlen($full_name) < 2) {
        $errors['full_name'] = "Họ và tên phải có ít nhất 2 ký tự.";
    }
    
    if (empty($username)) {
        $errors['username'] = "Vui lòng nhập tên đăng nhập.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Tên đăng nhập phải có ít nhất 3 ký tự.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới.";
    }
    
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ.";
    }
    
    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Vui lòng xác nhận mật khẩu.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Mật khẩu xác nhận không khớp.";
    }
    
    // If no individual field errors, check for system-level errors
    if (empty(array_filter($errors))) {
        // Attempt to register user
        $userId = registerUser($conn, $username, $email, $password, $full_name);
        
        if ($userId) {
            $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
        } else {
            $errors['general'] = "Tên đăng nhập hoặc email đã tồn tại.";
        }
    }
}

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" data-testid="registration-form-container">
                <div class="card-header">
                    <h3 class="text-center" data-testid="registration-form-title">Đăng Ký Tài Khoản</h3>
                </div>                <div class="card-body">
                    <!-- General error message -->
                    <div class="custom-error-alert <?php echo empty($errors['general']) ? 'hidden' : ''; ?>" 
                         data-testid="general-error-message">
                        <?php echo $errors['general']; ?>
                    </div>
                    
                    <!-- Success message -->
                    <div class="custom-success-alert <?php echo empty($success) ? 'hidden' : ''; ?>" 
                         data-testid="success-message">
                        <?php echo $success; ?>                        <?php if (!empty($success)): ?>
                            <br><a href="login.php" class="btn" 
                                   data-testid="login-redirect-button">Đăng nhập ngay</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="" id="registration-form" data-testid="registration-form" 
                          <?php echo !empty($success) ? 'style="display: none;"' : ''; ?>>                        <!-- Full Name Field -->
                        <div class="mb-3" data-testid="full-name-group">
                            <label for="full_name" class="form-label" data-testid="full-name-label">
                                Họ và tên <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php echo !empty($errors['full_name']) ? 'has-error' : ($form_submitted && empty($errors['full_name']) ? 'has-success' : ''); ?>" 
                                   id="full_name" 
                                   name="full_name" 
                                   data-testid="full-name-input"
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                   autocomplete="name">
                            <div class="field-error-message <?php echo empty($errors['full_name']) ? 'hidden' : ''; ?>" 
                                 data-testid="full-name-error">
                                <?php echo $errors['full_name']; ?>
                            </div>
                        </div>
                        
                        <!-- Username Field -->
                        <div class="mb-3" data-testid="username-group">
                            <label for="username" class="form-label" data-testid="username-label">
                                Tên đăng nhập <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php echo !empty($errors['username']) ? 'has-error' : ($form_submitted && empty($errors['username']) ? 'has-success' : ''); ?>" 
                                   id="username" 
                                   name="username" 
                                   data-testid="username-input"
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   autocomplete="username">
                            <div class="field-error-message <?php echo empty($errors['username']) ? 'hidden' : ''; ?>" 
                                 data-testid="username-error">
                                <?php echo $errors['username']; ?>
                            </div>
                        </div>
                        
                        <!-- Email Field -->
                        <div class="mb-3" data-testid="email-group">
                            <label for="email" class="form-label" data-testid="email-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?php echo !empty($errors['email']) ? 'has-error' : ($form_submitted && empty($errors['email']) ? 'has-success' : ''); ?>" 
                                   id="email" 
                                   name="email" 
                                   data-testid="email-input"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   autocomplete="email">
                            <div class="field-error-message <?php echo empty($errors['email']) ? 'hidden' : ''; ?>" 
                                 data-testid="email-error">
                                <?php echo $errors['email']; ?>
                            </div>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="mb-3" data-testid="password-group">
                            <label for="password" class="form-label" data-testid="password-label">
                                Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control <?php echo !empty($errors['password']) ? 'has-error' : ($form_submitted && empty($errors['password']) ? 'has-success' : ''); ?>" 
                                   id="password" 
                                   name="password" 
                                   data-testid="password-input"
                                   autocomplete="new-password">
                            <div class="field-error-message <?php echo empty($errors['password']) ? 'hidden' : ''; ?>" 
                                 data-testid="password-error">
                                <?php echo $errors['password']; ?>
                            </div>
                            <div class="form-text" data-testid="password-help">
                                Mật khẩu phải có ít nhất 6 ký tự.
                            </div>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div class="mb-3" data-testid="confirm-password-group">
                            <label for="confirm_password" class="form-label" data-testid="confirm-password-label">
                                Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control <?php echo !empty($errors['confirm_password']) ? 'has-error' : ($form_submitted && empty($errors['confirm_password']) ? 'has-success' : ''); ?>" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   data-testid="confirm-password-input"
                                   autocomplete="new-password">
                            <div class="field-error-message <?php echo empty($errors['confirm_password']) ? 'hidden' : ''; ?>" 
                                 data-testid="confirm-password-error">
                                <?php echo $errors['confirm_password']; ?>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" data-testid="register-submit">
                                Đăng Ký
                            </button>
                        </div>
                    </form>
                    
                    <!-- Login Link -->
                    <div class="text-center mt-3" data-testid="login-link-container">
                        <p>Đã có tài khoản? 
                           <a href="login.php" data-testid="login-link">Đăng nhập ngay</a>
                        </p>
                    </div>                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registration-form');
    const inputs = {
        fullName: document.getElementById('full_name'),
        username: document.getElementById('username'),
        email: document.getElementById('email'),
        password: document.getElementById('password'),
        confirmPassword: document.getElementById('confirm_password')
    };
    
    const errorElements = {
        fullName: document.querySelector('[data-testid="full-name-error"]'),
        username: document.querySelector('[data-testid="username-error"]'),
        email: document.querySelector('[data-testid="email-error"]'),
        password: document.querySelector('[data-testid="password-error"]'),
        confirmPassword: document.querySelector('[data-testid="confirm-password-error"]')
    };
    
    // Validation functions
    function validateFullName(value) {
        if (!value.trim()) {
            return 'Vui lòng nhập họ và tên.';
        }
        if (value.trim().length < 2) {
            return 'Họ và tên phải có ít nhất 2 ký tự.';
        }
        return '';
    }
    
    function validateUsername(value) {
        if (!value.trim()) {
            return 'Vui lòng nhập tên đăng nhập.';
        }
        if (value.trim().length < 3) {
            return 'Tên đăng nhập phải có ít nhất 3 ký tự.';
        }
        if (!/^[a-zA-Z0-9_]+$/.test(value.trim())) {
            return 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới.';
        }
        return '';
    }
    
    function validateEmail(value) {
        if (!value.trim()) {
            return 'Vui lòng nhập email.';
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value.trim())) {
            return 'Email không hợp lệ.';
        }
        return '';
    }
    
    function validatePassword(value) {
        if (!value) {
            return 'Vui lòng nhập mật khẩu.';
        }
        if (value.length < 6) {
            return 'Mật khẩu phải có ít nhất 6 ký tự.';
        }
        return '';
    }
    
    function validateConfirmPassword(password, confirmPassword) {
        if (!confirmPassword) {
            return 'Vui lòng xác nhận mật khẩu.';
        }
        if (password !== confirmPassword) {
            return 'Mật khẩu xác nhận không khớp.';
        }
        return '';
    }    // Show/hide error messages
    function showError(field, message) {
        const errorElement = errorElements[field];
        const inputElement = inputs[field];
        
        if (errorElement && inputElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
            inputElement.classList.add('has-error');
            inputElement.classList.remove('has-success');
        }
    }
    
    function hideError(field) {
        const errorElement = errorElements[field];
        const inputElement = inputs[field];
        
        if (errorElement && inputElement) {
            errorElement.classList.add('hidden');
            inputElement.classList.remove('has-error');
            inputElement.classList.add('has-success');
        }
    }
    
    // Real-time validation on blur
    inputs.fullName.addEventListener('blur', function() {
        const error = validateFullName(this.value);
        if (error) {
            showError('fullName', error);
        } else {
            hideError('fullName');
        }
    });
    
    inputs.username.addEventListener('blur', function() {
        const error = validateUsername(this.value);
        if (error) {
            showError('username', error);
        } else {
            hideError('username');
        }
    });
    
    inputs.email.addEventListener('blur', function() {
        const error = validateEmail(this.value);
        if (error) {
            showError('email', error);
        } else {
            hideError('email');
        }
    });
    
    inputs.password.addEventListener('blur', function() {
        const error = validatePassword(this.value);
        if (error) {
            showError('password', error);
        } else {
            hideError('password');
            // Also revalidate confirm password if it has a value
            if (inputs.confirmPassword.value) {
                const confirmError = validateConfirmPassword(this.value, inputs.confirmPassword.value);
                if (confirmError) {
                    showError('confirmPassword', confirmError);
                } else {
                    hideError('confirmPassword');
                }
            }
        }
    });
    
    inputs.confirmPassword.addEventListener('blur', function() {
        const error = validateConfirmPassword(inputs.password.value, this.value);
        if (error) {
            showError('confirmPassword', error);
        } else {
            hideError('confirmPassword');
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        
        // Validate all fields
        const fullNameError = validateFullName(inputs.fullName.value);
        const usernameError = validateUsername(inputs.username.value);
        const emailError = validateEmail(inputs.email.value);
        const passwordError = validatePassword(inputs.password.value);
        const confirmPasswordError = validateConfirmPassword(inputs.password.value, inputs.confirmPassword.value);
        
        if (fullNameError) {
            showError('fullName', fullNameError);
            hasErrors = true;
        } else {
            hideError('fullName');
        }
        
        if (usernameError) {
            showError('username', usernameError);
            hasErrors = true;
        } else {
            hideError('username');
        }
        
        if (emailError) {
            showError('email', emailError);
            hasErrors = true;
        } else {
            hideError('email');
        }
        
        if (passwordError) {
            showError('password', passwordError);
            hasErrors = true;
        } else {
            hideError('password');
        }
        
        if (confirmPasswordError) {
            showError('confirmPassword', confirmPasswordError);
            hasErrors = true;
        } else {
            hideError('confirmPassword');
        }
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php include "includes/footer.php"; ?>
