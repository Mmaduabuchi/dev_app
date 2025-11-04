<?php
//start session
session_start();
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
    //get the input data
    $notification_id = $_POST['nid'] ?? '';
    $usertoken = $_POST['token'] ?? '';

    //validate input data
    if (empty($usertoken) || empty($notification_id)) {
        sendResponse('error', 'Invalid user request.');
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
        $stmt = $conn->prepare("SELECT usertoken, suspended_at FROM `users` WHERE id = ?");
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
        $suspended_at = $row['suspended_at'];
        $stmt->close();

        // Verify the user token
        if ($usertoken !== $currentUserToken) {
            sendResponse('error', 'Invalid user token.');
        }

        //Check if user is still active
        if (!is_null($suspended_at)) {
            sendResponse('error', 'Account is suspended. Action not allowed.');
        }

        //update deleted_at to initialize soft delete
        $stmt = $conn->prepare("UPDATE `notifications` SET deleted_at = NOW() WHERE sender_id = ? AND id = ?");
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("ii", $userId, $notification_id);
        if(!$stmt->execute()){
            throw new Exception('Failed to update record: ' . $stmt->error);
        }
        $stmt->close();

        // If DB update is successful, commit transaction
        $conn->commit();

        //return success response
        sendResponse('success', 'Deleted request successfully.');

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }


}