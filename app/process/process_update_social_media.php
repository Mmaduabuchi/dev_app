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
    $github = $_POST['github'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $usertoken = $_POST['token'] ?? '';

    //validate inputs
    if (empty($github) || empty($linkedin)) {
        sendResponse('error', 'All fields are required.');
    }
    //validate usertoken
    if(empty($usertoken)){
        sendResponse('error', 'Invalid user token.');
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

        //verify usertoken matches the logged in user
        $stmt = $conn->prepare("SELECT id FROM `users` WHERE id = ? AND usertoken = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("is", $userId, $usertoken);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            sendResponse('error', 'Invalid user token.');
        }
        $stmt->close();

        //check if user account is suspended
        $stmt = $conn->prepare("SELECT suspended_at FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['suspended_at'] != null) {
            sendResponse('error', 'Account is suspended. Cannot update social media links.');
        }
        $stmt->close();

        //prepare update statement
        $stmt = $conn->prepare("UPDATE `developers_profiles` SET github = ?, linkedin = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("ssi", $github, $linkedin, $userId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception('No changes made to social media links.');
        }
        $stmt->close();

        //commit transaction
        $conn->commit();
        sendResponse('success', 'Social media links updated successfully.');

    }catch(Exception $e){
        //rollback transaction
        $conn->rollback();
        sendResponse('error', $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}