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
    $usertoken = $_POST['token'] ?? '';
    //get profile picture file
    $profile_picture = $_FILES['profile_picture'] ?? null;
    //validate input data
    if (empty($usertoken)) {
        sendResponse('error', 'Token is required.');
    }
    if ($profile_picture === null || $profile_picture['error'] !== UPLOAD_ERR_OK) {
        sendResponse('error', 'Error uploading file.');
    }
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }
    //validate file type and size (allow only JPG, PNG, WEBP, GIF up to 5MB)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    //check file type and size
    if (!in_array($profile_picture['type'], $allowedTypes) || $profile_picture['size'] > $maxFileSize) {
        sendResponse('error', 'Invalid profile photo. Only JPG, PNG, WEBP files under 5MB are allowed.');
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

        // Verify the provided token matches the one in the database
        if ($usertoken !== $currentUserToken) {
            sendResponse('error', 'Invalid user token.');
        }

        // Fetch current profile picture path to delete old file if exists
        $stmt = $conn->prepare("SELECT picture FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }
        $result = $stmt->get_result();
        $pictureRow = $result->fetch_assoc();
        $currentPicturePath = $pictureRow['picture'] ?? null;
        $stmt->close();

        if ($currentPicturePath) {
            // Delete the old profile picture file
            $oldFilePath = __DIR__ . '/../../' . $currentPicturePath;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Process and move the uploaded file
        $uploadDir = __DIR__ . '/../../library/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        //get file extension
        $pictureExt  = strtolower(pathinfo($profile_picture['name'], PATHINFO_EXTENSION));
        // Generate unique file name
        $pictureName = uniqid('profile_', true) .  time() . '.' . $pictureExt;
        //create path
        $picturePath = $uploadDir . $pictureName;

        // Move the uploaded file to the designated directory
        if (!move_uploaded_file($profile_picture['tmp_name'], $picturePath)) {
            throw new Exception('Failed to move uploaded file.');
        }
        // Update the user's profile picture path in the database
        $relativePicturePath = 'library/documents/' . $pictureName;
        $stmt = $conn->prepare("UPDATE `developers_profiles` SET profile_picture = ? WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $relativePicturePath, $userId);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Successful response
        sendResponse('success', 'Profile picture updated successfully.');

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }

}