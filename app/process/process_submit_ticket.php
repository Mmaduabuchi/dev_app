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
    //get data from input
    $ticketTitle = $_POST['title'] ?? '';
    $ticketCategory = $_POST['category'] ?? '';
    $ticketPriority = $_POST['priority'] ?? '';
    $ticketMessage = $_POST['message'] ?? '';

    //validate inputs
    if (empty($ticketTitle) || empty($ticketCategory) || empty($ticketPriority) || empty($ticketMessage)) {
        sendResponse('error', 'All fields are required.');
    }

    // Limit length
    if (strlen($ticketTitle) > 150) {
        throw new Exception('Ticket title is too long. Maximum allowed is 150 characters.');
    }
    if (strlen($ticketMessage) > 1000) {
        throw new Exception('Ticket message is too long. Maximum allowed is 1000 characters.');
    }

    // Strip HTML tags but allow safe ones if needed
    $ticketMessage = strip_tags($ticketMessage, '<b><i><u><br><p>');
    $ticketTitle = strip_tags($ticketTitle);

    // Convert special characters to HTML entities to prevent XSS
    $ticketMessage = htmlspecialchars($ticketMessage, ENT_QUOTES, 'UTF-8');
    $ticketTitle = htmlspecialchars($ticketTitle, ENT_QUOTES, 'UTF-8');

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];
    //get usertoken from session
    $usertoken = $_SESSION['user']['usertoken'] ?? null;

    if ($usertoken === null) {
        sendResponse('error', 'Can not proceed invalid token try again later.');
    }

    try {
        //begin transaction
        $conn->begin_transaction();

        //fetch the employer usertoken from database
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
        $user_token = $row['usertoken'];
        $suspended_at = $row['suspended_at'];
        $stmt->close();

        if ($suspended_at !== null) {
            sendResponse('error', 'Account has been suspended, please contact admin.');
        }

        if ($user_token !== $usertoken) {
            sendResponse('error', 'Invalid UserToken, try again later.');
        }


        // Generate a unique ticket reference
        do {
            $ticketReference = 'TCK-' . strtoupper(uniqid());

            // Check if this ticket reference already exists
            $checkStmt = $conn->prepare("SELECT id FROM `support_ticket` WHERE ticket_reference = ?");
            $checkStmt->bind_param("s", $ticketReference);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $exists = $result->num_rows > 0;
            $checkStmt->close();
        } while ($exists);

        //insert request into support_ticket database
        $stmt = $conn->prepare("INSERT INTO `support_ticket` (user_id, title, category, message, status, priority, ticket_reference, created_at, updated_at) VALUES (?, ?, ?, ?, 'pending', ?, ?, NOW(), NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("isssss", $userId, $ticketTitle, $ticketCategory, $ticketMessage, $ticketPriority, $ticketReference);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception('Failed to submit support ticket.');
        }
        $stmt->close();

        //commit transaction
        $conn->commit();

        sendResponse('success', 'Your support ticket has been submitted successfully. Our team will review it and respond as soon as possible.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}
