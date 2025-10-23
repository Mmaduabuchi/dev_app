<?php
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
    $usertoken = $_POST['token'] ?? '';
    $jobTitle = $_POST['jobTitle'] ?? '';
    $company = $_POST['company'] ?? '';
    $startyear = $_POST['startyear'] ?? '';
    $endyear = $_POST['endyear'] ?? '';
    $description = $_POST['description'] ?? '';

    //validate users inputs
    if(empty($usertoken) || empty($company) || empty($jobTitle) || empty($startyear) || empty($endyear) || empty($description)){
        sendResponse("error", "All fields are required.");
    }

    // multibyte-safe length
    if (mb_strlen($description, 'UTF-8') > 800) {
        sendResponse("error", "Job Description cannot exceed 800 characters.");
    }
    //validate company
    if (!preg_match("/^[a-zA-Z\s\.\,\-]+$/", $company)) {
        sendResponse("error", "company must contain only valid text.");
    }
    //validate jobTitle
    if (!preg_match("/^[a-zA-Z\s\.\,\-]+$/", $jobTitle)) {
        sendResponse("error", "Job title must contain only valid text.");
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

        //Check if user is still active
        $stmt = $conn->prepare("SELECT suspended_at FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $suspended_at = $row['suspended_at'];
        if (!is_null($suspended_at)) {
            sendResponse('error', 'Account is suspended. Action not allowed.');
        }
        $stmt->close();

        //check if the user has added upto five work experience records
        $stmt = $conn->prepare("SELECT COUNT(*) AS record_count FROM `work_experience_records` WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $record_count = $row['record_count'];
        if ($record_count >= 5) {
            sendResponse('error', 'You can only add up to five work experience records.');
        }
        $stmt->close();

        //insert work experience into work_experience_records table
        $stmt = $conn->prepare("INSERT INTO `work_experience_records` (user_id, job_title, company, start_year, end_year, job_description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("isssss", $userId, $jobTitle, $company, $startyear, $endyear, $description);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert work experience: ' . $stmt->error);
        }
        $stmt->close();
        //commit transaction
        $conn->commit();

        sendResponse('success', 'Work experience added successfully.');
    
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}