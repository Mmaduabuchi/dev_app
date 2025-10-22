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
    //get data from input
    $candidate_usertoken = $_POST['token'] ?? '';
    $request_title = $_POST['request_title'] ?? '';
    $request_message = $_POST['request_message'] ?? '';
    $request_type = $_POST['request_type'] ?? '';
    $request_email = $_POST['request_email'] ?? '';

    //validate inputs
    if (empty($candidate_usertoken) || empty($request_title) || empty($request_message) || empty($request_type) || empty($request_email)) {
        sendResponse('error', 'All fields are required.');
    }
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    try{
        //begin transaction
        $conn->begin_transaction();

        //fetch the employer usertoken from database
        $stmt = $conn->prepare("SELECT id, usertoken FROM `users` WHERE usertoken = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $candidate_usertoken);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            sendResponse('error', 'User not found.');
        }
        $row = $result->fetch_assoc();
        $candidateUserToken = $row['usertoken'];
        $candidate_id = $row['id'];
        $stmt->close();

        //fetch current user email from database
        $stmt = $conn->prepare("SELECT email FROM `users` WHERE id = ?");
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
        $current_user_email = $row['email'];
        $stmt->close();

        if($request_email !== $current_user_email){
            sendResponse('error', 'An error occurred while validating your email.');
        }

        //insert request into database
        $stmt = $conn->prepare("INSERT INTO `notifications` (user_id, sender_id, sender_email, title, message, type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("iissss", $candidate_id, $userId, $current_user_email, $request_title, $request_message, $request_type);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception('Failed to send request.');
        }
        $stmt->close();
        //commit transaction
        $conn->commit();
        sendResponse('success', 'Request sent successfully.');

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}