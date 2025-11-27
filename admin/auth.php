<?php
//strat session
session_start();
//include database connection
require_once __DIR__ . '/../config/databaseconnection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /devhire/admin/log/login');
    exit();
}
