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

    $action = $data['action'] ?? '';
    $userId = $data['userId'] ?? null;
    $reportId = $data['reportId'] ?? null;

    if (!$action || !$reportId || (!$userId && $action !== "dismiss")) {
        response("error", "Invalid data.");
    }

    $action_data_arr = ["dismiss", "warn", "ban"];

    if (!in_array($action, $action_data_arr)) {
        response("error", "Invalid action.");
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    try {
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

        // Check if report exists and is unresolved
        $stmt = $conn->prepare("SELECT * FROM account_reports WHERE report_id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows < 1){
            throw new Exception("Report not found.");
        }
        $report_resolved_at = $result->fetch_assoc()['resolved_at'];
        $stmt->close();

        if ($report_resolved_at !== null) {
            throw new Exception("Report already resolved.");
        }

        // Perform actions
        if ($action === "warn") {
            // Add a warning for the user
            $stmt = $conn->prepare("INSERT INTO user_warnings (user_id, report_id, created_at) VALUES (?, ?, NOW())");
            if ($stmt === false) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("is", $userId, $reportId);
            $stmt->execute();
            $stmt->close();

        } elseif ($action === "ban") {

            //get reported user
            $stmt = $conn->prepare("SELECT suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
            if($stmt === false){
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows < 1){
                throw new Exception("Reported user not found on the system.");
            }
            $user_suspended_at = $result->fetch_assoc()['user_suspended_at'];
            $stmt->close();

            if ($user_suspended_at !== null) {
                throw new Exception('Account is already suspended. Action not allowed.');
            }

            // Ban the user
            $stmt = $conn->prepare("UPDATE users SET status = 'banned', suspended_at = NOW() WHERE id = ?");
            if ($stmt === false) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
        }
        
        // Mark report as resolved
        $stmt = $conn->prepare("UPDATE account_reports SET status = 'resolved', resolved_at = NOW() WHERE report_id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $reportId);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        response("success", "Action executed successfully.");

    } catch (Exception $e) {
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