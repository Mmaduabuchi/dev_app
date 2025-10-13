<?php
// Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
    //get the input data
    $oldpassword = $_POST['oldpassword'] ?? '';
    $newpassword = $_POST['newpassword'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';

    //validate input data
    if (empty($oldpassword) || empty($newpassword) || empty($confirmpassword)) {
        sendResponse('error', 'All fields are required.');
    }
    if ($newpassword !== $confirmpassword) {
        sendResponse('error', 'New password and Confirm password do not match.');
    }
    if (strlen($newpassword) < 6) {
        sendResponse('error', 'New password must be at least 6 characters long.');
    }
    //check if passsword contains at least one number and one letter
    if (!preg_match('/[A-Za-z]/', $newpassword) || !preg_match('/[0-9]/', $newpassword)) {
        sendResponse('error', 'New password must contain at least one letter and one number.');
    }
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    try {
        // Fetch the current password hash from the database
        $stmt = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
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
        $currentPasswordHash = $row['password'];
        $stmt->close();

        // Verify the old password
        if (!password_verify($oldpassword, $currentPasswordHash)) {
            sendResponse('error', 'Old password is incorrect.');
        }
        
        // Hash the new password
        $newPasswordHash = password_hash($newpassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $newPasswordHash, $userId);
        if ($stmt->execute()) {
            sendResponse('success', 'Password updated successfully.');
        } else {
            sendResponse('error', 'Failed to update password. Please try again.');
        }
    } catch (Exception $e) {
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
    }
}