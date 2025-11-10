<?php
// Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

//helper function for response
function sendResponse($status, $message)
{
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
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
    if (!preg_match("/^\+?[0-9]{7,15}$/", $phone)) {
        sendResponse('error', 'Invalid phone number format.');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }
    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    try {
        // Start transaction
        $conn->begin_transaction();

        // Fetch the current password hash from the database
        $stmt = $conn->prepare("SELECT password, suspended_at, user_type, role FROM `users` WHERE id = ?");
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
        $suspended_at = $user['suspended_at'];
        $user_type = $user['user_type'];
        $role = $user['role'];
        $stmt->close();

        // Verify the current password
        if (!password_verify($currentpassword, $user['password'])) {
            sendResponse('error', 'Current password is incorrect.');
        }

        if ($suspended_at !== null) {
            sendResponse('error', 'Account has been suspended, please contact admin.');
        }

        if ($user_type === "employer" && $role === "CEO") {
            // Update user data in the database
            $stmt = $conn->prepare("UPDATE `users` SET fullname = ?, tel = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("ssi", $fullname, $phone, $userId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update user data in the database
            $stmt = $conn->prepare("UPDATE `users` SET fullname = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("si", $fullname, $userId);
            $stmt->execute();
            $stmt->close();

            //user phone on developers_profiles table
            $stmt = $conn->prepare("UPDATE `developers_profiles` SET phone_number = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("si", $phone, $userId);
            $stmt->execute();
        }

        // If DB update is successful, commit transaction
        $conn->commit();

        // Update session data
        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['phone'] = $phone;

        sendResponse('success', 'User data updated successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An unexpected error occurred. Please try again later.');
        error_log($e->getMessage());
    } finally {
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        $conn->close();
    }
}
