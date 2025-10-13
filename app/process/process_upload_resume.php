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
    //get usertoken form input
    $usertoken = $_POST['usertoken'] ?? '';
    //get resume file
    $resume = $_FILES['resume'] ?? null;

    //validate input data
    if (empty($usertoken)) {
        sendResponse('error', 'Token is required.');
    }
    if ($resume === null || $resume['error'] !== UPLOAD_ERR_OK) {
        sendResponse('error', 'Error uploading file.');
    }
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }
    //validate file type and size (allow only PDF, DOC, DOCX up to 5MB)
    $allowedTypes = ['application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    //check file type and size
    if (!in_array($resume['type'], $allowedTypes) || $resume['size'] > $maxFileSize) {
        sendResponse('error', 'Invalid profile photo. Only JPG, PNG, WEBP, GIF files under 5MB are allowed.');
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

        // Fetch current resume path to delete old file if exists
        $stmt = $conn->prepare("SELECT resume FROM `developers_profiles` WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to fetch resume: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $resumeRow = $result->fetch_assoc();
        $resumePath = $resumeRow['resume'] ?? null;

        //check if resume exists
        if($resumePath){
            //return error
            sendResponse('error', 'Please delete the existing resume before uploading a new one.');
        }
        $stmt->close();

        // Handle file upload
        $uploadDir = __DIR__ . '/../../library/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        //get file extension
        $resumeExt  = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));
        // Generate unique file name
        $resumeName = uniqid('resume_', true) .  time() . '.' . $resumeExt;
        //create path
        $resumePath = $uploadDir . $resumeName;
        
        // Move the uploaded file to the designated directory
        if (!move_uploaded_file($resume['tmp_name'], $resumePath)) {
            throw new Exception('Failed to move uploaded file.');
        }

        // Update database with new resume path
        $stmt = $conn->prepare("UPDATE `developers_profiles` SET resume = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $resumeName, $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update resume: ' . $stmt->error);
        }
        $stmt->close();
        // If DB update is successful, commit transaction
        $conn->commit();

        //return success response
        sendResponse('success', 'Resume uploaded successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}