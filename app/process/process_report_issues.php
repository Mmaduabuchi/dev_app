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
    //get user inputs
    $usertoken = $_POST["usertoken"] ?? '';
    $name = $_POST["name"] ?? '';
    $email = $_POST["email"] ?? '';
    $reportTitle = $_POST["reportTitle"] ?? '';
    $reportData = $_POST["reportData"] ?? '';

    //validate
    if(empty($name) || empty($email) || empty($reportTitle) || empty($reportData)){
        sendResponse("error", "All fields are required.");
    }

    // multibyte-safe length
    if (mb_strlen($reportData, 'UTF-8') > 1000) {
        sendResponse("error", "Report cannot exceed 1000 characters.");
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];
    try{
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

        //insert into reports
        $stmt = $conn->prepare("INSERT INTO `reports` (`user_id`, `fullname`, `email`, `report_title`, `report_data`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $name, $email, $reportTitle, $reportData);
        //execute
        if(!$stmt->execute()){
            throw new Exception('Failed to insert report: ' . $stmt->error);
        }
        $stmt->close();

        // If DB insert is successful, commit transaction
        $conn->commit();

        //return success response
        sendResponse('success', 'Submitted report data successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}