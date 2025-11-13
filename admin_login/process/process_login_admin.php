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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        sendResponse("error", "Email is required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse('error', 'Invalid email format.');
    }

    if (empty($password)) {
        sendResponse("error", "Password is required.");
    }

    try {

        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        // Check if user exists
        if ($result->num_rows === 0) {
            sendResponse('error', 'Invalid email or password.');
        }
        $admin_details = $result->fetch_assoc();

        //check authority
        if ($admin_details['user_type'] !== 'admin' && $admin_details['role'] !== 'administrator') {
            sendResponse('error', 'Access denied. Not an admin user.');
        }

        // Verify password
        if ($admin_details && password_verify($password, $admin_details['password'])) {
            // Regenerate session for security
            session_regenerate_id(true);
            unset($admin_details['password']);

            // Set session variables
            $_SESSION['admin'] = $admin_details;

            sendResponse('success', 'Login successful.');
        } else {
            sendResponse('error', 'Invalid email or password.');
        }

        $stmt->close();
    } catch (Exception $e) {
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}
