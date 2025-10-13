<?php
require_once __DIR__ . '/config/databaseconnection.php';
//strat session
session_start();
//check if user is logged in
if (!isset($_SESSION['user'])) {
    //if not redirect to dashboard page
    header("Location: /devhire/login");
    exit;
} else {
    //get user data from session
    $user = $_SESSION['user'];
    $role = $user['role'] ?? null;

    //if role is not set redirect to role selection page
    if (!is_null($role) || !empty($role)) {
        //if not redirect to dashboard page
        header("Location: /devhire/dashboard");
        exit;
    }
}
