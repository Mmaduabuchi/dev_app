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
    $reportId = $data['report_id'] ?? null;

    if (!$reportId) {
        response("error", "Invalid report ID.");
    }
        
    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;
    if (!$current_admin_id || !is_numeric($current_admin_id)) {
        response('error', 'Invalid session state.');
    }

    $stmt = null;
    try {
        //start transaction
        $conn->begin_transaction();

        //validate authrization
        $stmt = $conn->prepare("SELECT email, user_type, auth, suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $current_admin_id);
        if (!$stmt->execute()) {
            throw new Exception('Database execute failed.');
        }
        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            throw new Exception('Administrator not found.');
        }

        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_email = $admin['email'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin", "moderator"];
        if (!in_array($admin_type, $allowed_admin, true) || !in_array($admin_auth, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }
        $stmt->close();

        // Soft delete the report
        $stmt = $conn->prepare("UPDATE reports SET deleted_at = NOW() WHERE id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $reportId);
        
        if (!$stmt->execute()) {
            throw new Exception('Execution failed: ' . $stmt->error);
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('Report not found or already deleted.');
        }
        $stmt->close();

        //commit transaction
        $conn->commit();

        response('success', 'Report deleted successfully.');

    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        response('error', $e->getMessage());
    } finally {
        // Close the database connection
        if($stmt !== null){
            $stmt->close();
        }
        $conn->close();
    }

} else {
    response('error', 'Invalid request method.');
}