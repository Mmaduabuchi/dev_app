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
    $usertoken = $_POST['usertoken'] ?? '';

    //validate input data
    if (empty($usertoken)) {
        sendResponse('error', 'Token is required.');
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

        // Fetch current resume path
        $stmt = $conn->prepare("SELECT resume FROM `developers_profiles` WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to fetch resume: ' . $stmt->error);
        }
        $res = $stmt->get_result();
        $resumeRow = $res->fetch_assoc();
        $resumePath = $resumeRow['resume'] ?? null;
        $stmt->close();

        // Proceed to delete resume data
        $stmt = $conn->prepare("UPDATE `developers_profiles` SET resume = NULL WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete resume: ' . $stmt->error);
        }
        $stmt->close();
        
        // If DB update is successful, commit transaction
        $conn->commit();

        // Now delete file from filesystem (after commit to avoid DB rollback/file mismatch)
        if (!empty($resumePath) && file_exists($resumePath)) {
            unlink($resumePath);
        }

        sendResponse('success', 'Resume deleted successfully.');
    } catch (Exception $e) {
        sendResponse('error', $e->getMessage());
    }
    $conn->close();
}