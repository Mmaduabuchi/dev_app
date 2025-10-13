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
    //get the input data
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $currentpassword = $_POST['currentpassword'] ?? '';
    //validate input data
    if (empty($fullname) || empty($phone) || empty($currentpassword)) {
        sendResponse('error', 'All fields are required.');
    }
    //validate fullname (only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        sendResponse('error', 'Full name can only contain letters and spaces.');
    }
    //validate phone (only numbers and +, -, space)
    if (!preg_match("/^[0-9+\-\s]+$/", $phone)) {
        sendResponse('error', 'Phone number can only contain numbers, spaces, + and -.');
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
        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify the current password
        if (!password_verify($currentpassword, $user['password'])) {
            sendResponse('error', 'Current password is incorrect.');
        }

        // Update user data in the database
        $stmt = $conn->prepare("UPDATE `users` SET fullname = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $fullname, $userId);
        if ($stmt->execute()) {
            //user phone on developers_profiles table
            $stmt = $conn->prepare("UPDATE `developers_profiles` SET phone_number = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("si", $phone, $userId);
            $stmt->execute();
            $stmt->close();

            // Update session data
            $_SESSION['user']['fullname'] = $fullname;
            $_SESSION['user']['phone'] = $phone;

            sendResponse('success', 'User data updated successfully.');
        } else {
            throw new Exception('Failed to update user data: ' . $stmt->error);
        }
    } catch (Exception $e) {
        sendResponse('error', $e->getMessage());
    } finally {
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
    }
}