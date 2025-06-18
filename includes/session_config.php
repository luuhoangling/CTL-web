<?php
/**
 * Session Configuration
 * This file should be included before any session operations
 */

// Prevent session fixation attacks
if (session_status() == PHP_SESSION_NONE) {
    // Configure session parameters before starting session
    
    // Set session cookie parameters for security
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true; // Prevent JavaScript access to session cookie
    $samesite = 'Lax'; // CSRF protection
    
    // Set session cookie params
    session_set_cookie_params([
        'lifetime' => 3600, // 1 hour
        'path' => '/',
        'domain' => '', // Let browser determine domain
        'secure' => $secure, // Use secure cookies on HTTPS
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
    
    // Configure session settings
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.use_only_cookies', 1); // Only use cookies for session ID
    ini_set('session.use_trans_sid', 0); // Don't pass session ID in URL
    ini_set('session.entropy_length', 32); // Better random number generation
    ini_set('session.hash_bits_per_character', 6); // More compact session IDs
    
    // Start the session
    session_start();
    
    // Regenerate session ID on login to prevent session fixation
    if (isset($_SESSION['regenerate_id']) && $_SESSION['regenerate_id']) {
        session_regenerate_id(true);
        unset($_SESSION['regenerate_id']);
    }
}
?>
