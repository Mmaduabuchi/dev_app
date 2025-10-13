<?php
require_once __DIR__ . '/config/databaseconnection.php';
//strat session
session_start();
//check if user is logged in
if (isset($_SESSION['user']) && $_SESSION['user']['auth'] === 'user') {
    //if not redirect to dashboard page
    header("Location: /devhire/dashboard");
    exit;
}