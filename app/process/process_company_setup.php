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
    //get company details
    $CompanyIndustry = $_POST['CompanyIndustry'] ?? null;
    $CompanyWebsite = $_POST['CompanyWebsite'] ?? null;
    $CompanyBio = $_POST['CompanyBio'] ?? null;

    //get CompanyLogo file
    $CompanyLogo = $_FILES['CompanyLogo'] ?? null;
    if ($CompanyLogo === null || $CompanyLogo['error'] !== UPLOAD_ERR_OK) {
        sendResponse('error', 'Error uploading file.');
    }

    if(!$CompanyIndustry || !$CompanyBio){
        sendResponse('error', 'Company Industry or Bio are required.');
    }

    // Validate CompanyBio length (max 800 characters)
    if (is_array($CompanyBio)) {
        sendResponse('error', 'Invalid Company Bio.');
    }
    $CompanyBio = trim((string)$CompanyBio);
    if ($CompanyBio !== '' && mb_strlen($CompanyBio, 'UTF-8') > 1500) {
        sendResponse('error', 'Company Bio must not exceed 1500 characters.');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    //validate file type and size (allow only JPG, PNG, WEBP, GIF up to 5MB)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    //check file type and size
    if (!in_array($CompanyLogo['type'], $allowedTypes) || $CompanyLogo['size'] > $maxFileSize) {
        sendResponse('error', 'Invalid company logo photo. Only JPG, PNG, WEBP files under 5MB are allowed.');
    }
    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    try{
        // Start transaction
        $conn->begin_transaction();

        // Fetch the user data from the database
        $stmt = $conn->prepare("SELECT suspended_at FROM `users` WHERE id = ?");
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
        $suspended_at = $row['suspended_at'];
        $stmt->close();

        //check user account status
        if($suspended_at !== null){
            sendResponse('error', 'Your account has been suspended.');
        }

        // Fetch the user onboarding_id
        $stmt = $conn->prepare("SELECT onboarding_id FROM `onboarding_to_users` WHERE user_id = ?");
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
        $onboarding_id = $row['onboarding_id'];
        $stmt->close();

        if(!$onboarding_id){
            sendResponse('error', 'Invalid Onboarding token id.');
        }

        // Fetch the user company_size
        $stmt = $conn->prepare("SELECT * FROM `onboarding_sessions` WHERE onboarding_id = ? AND field_name = 'company_size' LIMIT 1");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $onboarding_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            sendResponse('error', 'User not found.');
        }
        $row = $result->fetch_assoc();
        $CompanySize = $row['field_value'];
        $stmt->close();

        // Process and move the uploaded file
        $uploadDir = __DIR__ . '/../../library/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        //get file extension
        $LogoExt  = strtolower(pathinfo($CompanyLogo['name'], PATHINFO_EXTENSION));
        // Generate unique file name
        $LogoName = uniqid('logo_', true) .  time() . '.' . $LogoExt;
        //create path
        $LogoPath = $uploadDir . $LogoName;

        // Move the uploaded file to the designated directory
        if (!move_uploaded_file($CompanyLogo['tmp_name'], $LogoPath)) {
            throw new Exception('Failed to move uploaded file.');
        }
        // Update the user's profile picture path in the database
        $relativeLogoPath = 'library/documents/' . $LogoName;
        //employer action status
        $action = "completed";
        $stmt = $conn->prepare("UPDATE `employer_profiles` SET company_size = ?, industry = ?, website = ?, company_logo = ?, bio = ?, action = ?, updated_at = NOW() WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("ssssssi", $CompanySize, $CompanyIndustry, $CompanyWebsite, $relativeLogoPath, $CompanyBio, $action, $userId);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Successful response
        sendResponse('success', 'Setup completed successfully.');

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }

}