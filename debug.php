<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Simple debug page to check session and CSRF functionality
echo "<h2>Debug Information</h2>";

echo "<h3>Session Information:</h3>";
echo "Session Status: " . session_status() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Started: " . (session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No') . "<br>";

echo "<h3>CSRF Token Information:</h3>";
$csrf_token = generateCSRFToken();
echo "CSRF Token Generated: " . $csrf_token . "<br>";
echo "CSRF Token in Session: " . (isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : 'Not Set') . "<br>";
echo "CSRF Token Time: " . (isset($_SESSION['csrf_token_time']) ? date('Y-m-d H:i:s', $_SESSION['csrf_token_time']) : 'Not Set') . "<br>";

echo "<h3>Server Information:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Yes' : 'No') . "<br>";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "<br>";

echo "<h3>Session Settings:</h3>";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "<br>";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "<br>";
echo "session.use_only_cookies: " . ini_get('session.use_only_cookies') . "<br>";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "<br>";

echo "<h3>Test CSRF Validation:</h3>";
if (isset($_POST['test_csrf'])) {
    $result = validateCSRFToken($_POST['csrf_token']);
    echo "CSRF Validation Result: " . ($result ? 'VALID' : 'INVALID') . "<br>";
}

echo "<h3>Test Form:</h3>";
?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <input type="submit" name="test_csrf" value="Test CSRF Token">
</form>

<br><br>
<a href="login.php">Back to Login</a>
