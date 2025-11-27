<?php
//start session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie (if any)
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


//destory session
session_destroy();

//redirect to login page
header("Location: /devhire/admin/log/login");
exit();
