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
    //get usertoken form inputs
    $usertoken = $_POST['usertoken'] ?? '';
    $course = $_POST['course'] ?? '';
    $degree = $_POST['degree'] ?? '';
    $academy = $_POST['academy'] ?? '';
    $startyear = $_POST['startyear'] ?? '';
    $endyear = $_POST['endyear'] ?? '';
    $description = $_POST['description'] ?? '';

    //validate users inputs
    if(empty($usertoken) || empty($course) || empty($degree) || empty($academy) || empty($startyear) || empty($endyear) || empty($description)){
        sendResponse("error", "All fields are required.");
    }

    // multibyte-safe length
    if (mb_strlen($description, 'UTF-8') > 800) {
        sendResponse("error", "Description cannot exceed 800 characters.");
    }
    //validate academy
    if (!preg_match("/^[a-zA-Z\s\.\,\-]+$/", $academy)) {
        sendResponse("error", "Academy must contain only valid text.");
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

        //Check if user already has an educational record
        $stmt = $conn->prepare("SELECT id FROM `education_records` WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            sendResponse('error', 'Educational record already exists.');
        }
        $stmt->close();

        // Insert into education_records table
        $stmt = $conn->prepare("INSERT INTO `education_records` 
            (user_id, course, degree, academy, start_year, end_year, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("isssiss", $userId, $course, $degree, $academy, $startyear, $endyear, $description);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert education record: ' . $stmt->error);
        }
        $stmt->close();

        // If DB update is successful, commit transaction
        $conn->commit();

        //return success response
        sendResponse('success', 'Education data saved Successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}