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
    $website = $_POST['website'] ?? '';
    $usertoken = $_POST['token'] ?? '';

    //validate inputs
    if (empty($website)) {
        sendResponse('error', 'Website field is required.');
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
        if (!is_null($row['suspended_at'])) {
            sendResponse('error', 'Account is suspended. Action not allowed.');
        }
        $stmt->close();

        //insert website into developer_profiles table
        $stmt = $conn->prepare("UPDATE `developers_profiles` SET website = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $website, $userId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception('Failed to add website. Please try again.');
        }
        $stmt->close();

        //commit transaction
        $conn->commit();

        sendResponse('success', 'Website added successfully.');
    }catch (Exception $e) {
        //rollback transaction on error
        $conn->rollback();
        sendResponse('error', $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}