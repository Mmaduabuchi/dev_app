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

// Fallback for getallheaders() if not available
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    //get data
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? null;

    $headers = getallheaders();
    $csrfToken = $headers['X-Csrf-Token'] ?? '';

    if (!isset($_SESSION['csrf_token']) || empty($csrfToken) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        sendResponse('error', 'Invalid CSRF token. Request denied..'. $csrfToken);
    }

    if ($action === null || $action !== 'cancel_subscription') {
        sendResponse('error', 'Action is required.');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

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

        //check subscription exists - fetch the latest
        $stmt = $conn->prepare("SELECT id, status FROM `subscriptions` WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $subscription = $result->fetch_assoc();
        $stmt->close();

        if (!$subscription) {
            sendResponse('error', 'No active subscription found.');
        }

        if ($subscription['status'] === 'cancelled') {
            sendResponse('error', 'Subscription is already cancelled.');
        }
        // Cancel subscription
        $stmt = $conn->prepare("UPDATE `subscriptions` SET status = 'cancelled', cancelled_at = NOW() WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $subscription['id']);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception('Failed to cancel subscription.');
        }

        $stmt->close();

        //commit transaction
        $conn->commit();

        sendResponse('success', 'Your subscription has been cancelled successfully.');

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}