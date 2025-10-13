<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
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
    //get user inputs
    $usertoken = $_POST["usertoken"] ?? '';
    $recordID = $_POST["recordID"] ?? '';

    //validate
    if(empty($recordID)){
        sendResponse("error", "Invalid request ID.");
    }

        // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];
    try{
        // Start transaction
        $conn->begin_transaction();

        // Fetch the user token from the database
        $stmt = $conn->prepare("SELECT usertoken FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            sendResponse('error', 'User not found.');
        }
        $row = $result->fetch_assoc();
        $currentUserToken = $row['usertoken'];
        $stmt->close();

        // Verify the user token
        if ($usertoken !== $currentUserToken) {
            sendResponse('error', 'Invalid user token.');
        }

        //update deleted_at to initialize soft delete
        $stmt = $conn->prepare("UPDATE `reports` SET deleted_at = NOW() WHERE user_id = ? AND id = ?");
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("ii", $userId, $recordID);
        if(!$stmt->execute()){
            throw new Exception('Failed to update record: ' . $stmt->error);
        }
        $stmt->close();

        // If DB update is successful, commit transaction
        $conn->commit();

        //return success response
        sendResponse('success', 'Deleted report record successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}