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

        // Proceed to delete user account and related data
        // Begin transaction
        $transactionStarted = $conn->begin_transaction();

        // Delete from developers_profiles
        $stmt = $conn->prepare("DELETE FROM `developers_profiles` WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete developer profile: ' . $stmt->error);
        }
        $stmt->close();

        // Delete from users
        $stmt = $conn->prepare("DELETE FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete user: ' . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Destroy session
        session_unset();
        session_destroy();
        //return a success response
        sendResponse('success', 'Account deleted successfully.');
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($transactionStarted) && $transactionStarted) {
            $conn->rollback();
        }
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        if (isset($stmt) && $stmt) {
            $stmt->close();
        }
        //Close database connection
        $conn->close();
    }
}