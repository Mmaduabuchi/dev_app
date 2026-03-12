<?php
// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

// Clear all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

//redirect request
header("location: /devhire/");
exit();