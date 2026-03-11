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
    $stmt->close();

    $admin_name = $admin['fullname'];
    $admin_email = $admin['email'];
    $admin_auth = $admin['auth'];

    //get reports count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM reports WHERE status IS NULL AND deleted_at IS NULL");
    if ($stmt === false) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception('Failed to retrieve result.');
    }

    $row = $result->fetch_assoc();
    $report_count = (int) ($row['count'] ?? 0);

    $stmt->close();

    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: /devhire/admin/dashboard/errorpage/error');
    exit();
}