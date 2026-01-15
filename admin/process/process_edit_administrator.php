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
    $administratorID = $_POST['admin_id'] ?? null;
    $administrator_email = $_POST['email'] ?? null;
    $administrator_new_password = $_POST['password'] ?? null;

    if (!$administratorID || !is_numeric($administratorID)) {
        response('error', 'Invalid administrator ID');
    }

    //validate email
    if (!filter_var($administrator_email, FILTER_VALIDATE_EMAIL)) {
        response('error', 'Invalid administrator email format.');
    }
    
    //validate password
    if (strlen($administrator_new_password) < 8) {
        response('error', 'Password must be at least 8 characters long.');
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    if (!$current_admin_id || !is_numeric($current_admin_id)) {
        response('error', 'Invalid session state.');
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

        $allowed_type = ["admin", "subadmin"];
        
        if (!in_array($admin_type, $allowed_type)) {
            response('error', 'Action not allowed.');
        }

        if (!in_array($admin_auth, $allowed_type)) {
            response('error', 'Action not allowed.');
        }
        $stmt->close();

        //fetch administrator to edit password data
        $stmt = $conn->prepare("SELECT id, password, auth, suspended_at FROM users WHERE id = ? AND email = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("is", $administratorID, $administrator_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            response('error', 'Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $target_password = $admin['password'];
        $target_auth = $admin['auth'];
        $target_admin_suspended_at = $admin['suspended_at'];

        if ($target_admin_suspended_at !== null) {
            response('error', 'Account is suspended. Action not allowed.');
        }

        if($target_auth == "admin" && $admin_auth == "subadmin"){
            response('error', 'Action not allowed, Can not change Super Admin password.');
        }

        // Check if the new password is the same as the old password
        if (password_verify($administrator_new_password, $target_password)) {
            response('error', 'New password cannot be the same as the old password.');
        }
        $stmt->close();

        //Hash the new password
        $new_password_hashed = password_hash($administrator_new_password, PASSWORD_DEFAULT);

        //Update the password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND deleted_at IS NULL");
        if ($stmt === false) {
            throw new Exception('Database update error: ' . $conn->error);
        }
        $stmt->bind_param("si", $new_password_hashed, $administratorID);
        if(!$stmt->execute()){
            throw new Exception('Database update error: ' . $conn->error);
        }
        $stmt->close();
        // Commit the transaction
        $conn->commit();
        response('success', 'Administrator password updated successfully');

    } catch (Exception $e) {
        //rollback transaction on error
        $conn->rollback();
        error_log($e->getMessage());
        response('error', 'Something went wrong. Please try again.');
    } finally {
        // Close the database connection
        $conn->close();
    }
} else {
    response('error', 'Invalid request method.');
}