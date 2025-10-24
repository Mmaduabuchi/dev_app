<?php
//start session
session_start();
//database connection
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
    //get usertoken and skill name from inputs
    $usertoken = $_POST['token'] ?? '';
    $skillId = $_POST['skillId'] ?? '';

    //validate users inputs
    if (empty($usertoken) || empty($skillId)) {
        sendResponse("error", "All fields are required.");
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

        // Verify usertoken matches the logged in user
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

        // Delete the skill for the user
        $stmt = $conn->prepare("DELETE FROM `user_skills` WHERE user_id = ? AND id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("ii", $userId, $skillId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            sendResponse('error', 'Skill not found or could not be deleted.');
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        sendResponse('success', 'Skill deleted successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}