<?php
//start session
session_start();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

//helper function for response
function sendResponse($status, $message) { 
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
}else{
    $report_reason = $_POST['report_reason'] ?? null;
    $report_message = trim($_POST['report_message'] ?? '');
    $reported_user_id = (int)($_POST['reported_user_id'] ?? 0);

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    //validate
    if(!$report_reason){
        sendResponse('error', 'Reason for Report is required.');
    }

    $report_message_arr = ["Fraud", "Abusive Behavior", "Spam", "Impersonation", "Other"];
    if (!in_array($report_reason, $report_message_arr)) {
        sendResponse('error', 'Invalid report reason.');
    }

    if (!empty($_POST['report_message']) && strlen($report_message) > 300) {
        sendResponse('error', 'Message must not exceed 300 characters.');
    }

    if ($report_reason === "Other" && $report_message === '') {
        sendResponse('error', 'Please provide a message when reason is "Other".');
    }

    if(!$reported_user_id){
        sendResponse('error', 'User ID is required and must be valid.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    try{
        // Start transaction
        $conn->begin_transaction();

        //verify logged in user
        $stmt = $conn->prepare("SELECT id, usertoken, suspended_at FROM `users` WHERE id = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Invalid user.');
        }
        $row = $result->fetch_assoc();
        if ($row['suspended_at'] !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }
        $stmt->close();

        //verify reported user
        $stmt = $conn->prepare("SELECT id, usertoken, report_count FROM `users` WHERE id = ? LIMIT 1 ");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $reported_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Invalid user account.');
        }
        $row = $result->fetch_assoc();
        $report_count = (int)$row['report_count'];
        $stmt->close();

        $stmt = $conn->prepare("SELECT id FROM account_reports WHERE reported_by = ? AND reported_user_id = ? AND reason = ? AND DATE(created_at) = CURDATE() LIMIT 1");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("iis", $userId, $reported_user_id, $report_reason);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            throw new Exception('You have already reported this user for the same reason today.');
        }
        $stmt->close();

        function generateReportId($length = 9) {
            $numbers = '0123456789';
            $id = '';
            for ($i = 0; $i < $length; $i++) {
                $id .= $numbers[random_int(0, 9)]; // cryptographically secure
            }
            return 'RPT-' . $id;
        }

        function generateUniqueReportId($conn, $length = 9) {
            do {
                $reportId = generateReportId($length);
                $stmt = $conn->prepare("SELECT id FROM account_reports WHERE report_id = ? LIMIT 1");
                $stmt->bind_param("s", $reportId);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
            } while ($result->num_rows > 0); // regenerate if exists
            return $reportId;
        }

        // Usage
        $report_id = generateUniqueReportId($conn);


        //Insert report log
        $stmt = $conn->prepare("INSERT INTO account_reports (reported_by, reported_user_id, reason, message, report_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("iisss", $userId, $reported_user_id, $report_reason, $report_message, $report_id);
        $stmt->execute();
        $stmt->close();

        $reportCountVar = $report_count + 1;

        //update report count
        $stmt = $conn->prepare("UPDATE `users` SET report_count = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $reportCountVar, $reported_user_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        //return success response
        sendResponse('success', 'Report submitted successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}