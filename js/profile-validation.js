// Profile form validation - Simple and effective
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile validation script loaded');
    
    // Only run on profile page
    if (!window.location.pathname.includes('profile.php')) {
        console.log('Not on profile page, exiting validation');
        return;
    }
    
    const form = document.querySelector('form[method="POST"]');
    if (!form) {
        console.log('No form found');
        return;
    }
    
    console.log('Form found, setting up validation');
    
    // Get form fields
    const fullNameField = document.getElementById('full_name');
    const emailField = document.getElementById('email');
    const currentPasswordField = document.getElementById('current_password');
    const newPasswordField = document.getElementById('new_password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    console.log('Form fields:', {
        fullName: !!fullNameField,
        email: !!emailField,
        currentPassword: !!currentPasswordField,
        newPassword: !!newPasswordField,
        confirmPassword: !!confirmPasswordField
    });
      // Error display functions
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Remove existing error
        clearError(fieldId);
        
        // Add error class and message
        field.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger small mt-1';
        errorDiv.textContent = message;
        errorDiv.setAttribute('data-testid', `${fieldId}-error`);
        field.parentNode.appendChild(errorDiv);
    }
    
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
    }
    
    function clearAllErrors() {
        ['full_name', 'email', 'current_password', 'new_password', 'confirm_password'].forEach(clearError);
    }
    
    // Form submit handler
    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        e.preventDefault(); // Always prevent default first
        
        clearAllErrors();
        let hasError = false;
        
        // Validate full name - REQUIRED
        const fullName = fullNameField.value.trim();
        console.log('Full name value:', `"${fullName}"`);
        
        if (fullName === '') {
            console.log('Full name is empty - showing error');
            showError('full_name', 'Họ và tên không được để trống.');
            hasError = true;
        } else if (fullName.length < 2) {
            showError('full_name', 'Họ và tên phải có ít nhất 2 ký tự.');
            hasError = true;
        } else if (fullName.length > 50) {
            showError('full_name', 'Họ và tên không được vượt quá 50 ký tự.');
            hasError = true;
        } else if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(fullName)) {
            showError('full_name', 'Họ và tên chỉ được chứa chữ cái và khoảng trắng.');
            hasError = true;
        }
        
        // Validate email - REQUIRED
        const email = emailField.value.trim();
        console.log('Email value:', `"${email}"`);
        
        if (email === '') {
            console.log('Email is empty - showing error');
            showError('email', 'Email không được để trống.');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Email không đúng định dạng.');
            hasError = true;
        } else if (email.length > 100) {
            showError('email', 'Email không được vượt quá 100 ký tự.');
            hasError = true;
        }
        
        // Validate password fields (optional)
        const currentPassword = currentPasswordField ? currentPasswordField.value : '';
        const newPassword = newPasswordField ? newPasswordField.value : '';
        const confirmPassword = confirmPasswordField ? confirmPasswordField.value : '';
        
        if (currentPassword !== '' || newPassword !== '' || confirmPassword !== '') {
            if (currentPassword === '') {
                showError('current_password', 'Vui lòng nhập mật khẩu hiện tại.');
                hasError = true;
            }
            
            if (newPassword === '') {
                showError('new_password', 'Vui lòng nhập mật khẩu mới.');
                hasError = true;
            } else if (newPassword.length < 6) {
                showError('new_password', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                hasError = true;
            } else if (newPassword.length > 50) {
                showError('new_password', 'Mật khẩu mới không được vượt quá 50 ký tự.');
                hasError = true;
            }
            
            if (confirmPassword === '') {
                showError('confirm_password', 'Vui lòng xác nhận mật khẩu mới.');
                hasError = true;
            } else if (confirmPassword !== newPassword) {
                showError('confirm_password', 'Mật khẩu xác nhận không khớp với mật khẩu mới.');
                hasError = true;
            }
        }
        
        console.log('Validation complete. Has errors:', hasError);
        
        // Submit form only if no errors
        if (!hasError) {
            console.log('No errors found - submitting form');
            // Remove event listener to prevent infinite loop
            form.removeEventListener('submit', arguments.callee);
            form.submit();
        } else {
            console.log('Errors found - not submitting');
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
    
    console.log('Profile form validation initialized');
});
