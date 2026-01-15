<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin authentication check
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        response('error', 'Unauthorized');
    }   

    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    $administratorID = $data['administrator_id'] ?? null;

    if (!$administratorID || !is_numeric($administratorID)) {
        response('error', 'Invalid administrator ID');
    }

    //get admin id and fullname
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    if (!$current_admin_id || !is_numeric($current_admin_id)) {
        response('error', 'Invalid session state.');
    }

    // Prevent self-deletion
    if ($administratorID == $current_admin_id) {
        response('error', 'You cannot delete your own account');
    }
    try{
        // Start a transaction
        $conn->begin_transaction();

        //validate authrization
        $stmt = $conn->prepare("SELECT user_type, auth, suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $current_admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            response('error', 'Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            response('error', 'Account is suspended. Action not allowed.');
        }
        
        if ($admin_type !== "admin") {
            response('error', 'Action not allowed.');
        }

        if ($admin_auth !== "admin") {
            response('error', 'Action not allowed.');
        }

        $stmt->close();

        //fetch administrator to be deleted data
        $stmt = $conn->prepare("SELECT user_type, auth FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $administratorID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            response('error', 'Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $targetRole = $admin['user_type'];
        $stmt->close();

        // Delete the administrator
        $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ? AND auth IN ('subadmin', 'moderator') AND deleted_at IS NULL");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $administratorID);
        if(!$stmt->execute()){
            throw new Exception('Database error: ' . $conn->error);
        }
        if ($stmt->affected_rows === 0) {
            throw new Exception('Administrator not found or action not permitted.');
        }
        $stmt->close();

        //register administrator account delete action log
        $logStmt = $conn->prepare("INSERT INTO admin_audit_logs (admin_id, admin_user_type, action, action_description, target_user_id, target_user_type, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($logStmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $action = 'DELETE_ADMIN';
        $description = 'Deleted sub-admin or moderator account';

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $logStmt->bind_param("isssisss", $current_admin_id, $admin_auth, $action, $description, $administratorID, $targetRole, $ip, $userAgent);

        if(!$logStmt->execute()){
            throw new Exception('Database logStmt error: ' . $conn->error);
        }
        $logStmt->close();

        // Commit the transaction
        $conn->commit();

        response('success', 'Administrator deleted successfully');
    } catch (Exception $e) {
        //rollback transaction on error
        $conn->rollback();
        error_log($e->getMessage()); // log internally
        response('error', 'Something went wrong. Please try again.');
    } finally {
        // Close the database connection
        $conn->close();
    }
} else {
    response('error', 'Invalid request method.');
}