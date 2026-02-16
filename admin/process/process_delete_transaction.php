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

    $data = json_decode(file_get_contents('php://input'), true);

    // Collect POST data
    $transactionId = $data['transactionId'] ?? null;

    // Validation
    if (!$transactionId) {
        response('error', 'Missing required fields.');
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

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
            throw new Exception('Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin)) {
            throw new Exception('Action not allowed.');
        }

        if (!in_array($admin_auth, $allowed_admin)) {
            throw new Exception('Action not allowed.');
        }
        
        // Delete the transaction
        $stmt = $conn->prepare("UPDATE transaction_history SET deleted_at = NOW(), deleted_by = ? WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("ii", $current_admin_id, $transactionId);
        $stmt->execute();
        $stmt->close();
        
        // Commit the transaction
        $conn->commit();

        //return success response
        response('success', 'Transaction deleted successfully.');
    }catch(Exception $e){
        //rollback transaction on error
        $conn->rollback();
        response("error", $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }

} else {
    response('error', 'Invalid request method.');
}