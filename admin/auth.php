<?php
//strat session
session_start();
//include database connection
require_once __DIR__ . '/../config/databaseconnection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /devhire/admin/log/login');
    exit();
}

//admin_id
$admin_id = $_SESSION['admin']['id'] ?? null;
$admin_name = $_SESSION['admin']['fullname'];
$admin_role = $_SESSION['admin']['role'];
$admin_email = $_SESSION['admin']['email'];

try{
    $administrator_arr = ["admin", "subadmin", "moderator"];
    //fetch admin details
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ? AND auth IN (?, ?, ?) LIMIT 1");
    if ($stmt === false) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("isss", $admin_id, $administrator_arr[0], $administrator_arr[1], $administrator_arr[2]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Admin not found');
    }
    $admin = $result->fetch_assoc();

    $admin_name = $admin['fullname'];
    $admin_email = $admin['email'];
    $admin_auth = $admin['auth'];

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: /devhire/admin/dashboard/errorpage/error');
    exit();
}