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

if (!isset($_SESSION['onboarding_id'])) {
    $_SESSION['onboarding_id'] = uniqid('onboard_', true);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    $onboarding_id = $_SESSION['onboarding_id'];
    $step_number = (int)$_POST['step_number'] ?? 1;
    $field_name = 'hire_type';
    $field_value = $_POST['field_value'] ?? '';

    //validate session
    if(!$onboarding_id){
        sendResponse('error', 'Invalid user session id.');
    }

    //validate step number
    if($step_number !== 4){
        sendResponse('error', 'Invalid onboarding stage access denied.');
    }
    if ($step_number < 1 || $step_number > 7) {
        sendResponse('error', 'Invalid onboarding stage access denied..');
    }
    // Validate and sanitize field_value
    $field_value = isset($_POST['field_value']) ? trim($_POST['field_value']) : '';
    if ($field_value === '') {
        sendResponse('error', 'Invalid option selected.');
    }
    if (!is_string($field_value)) {
        sendResponse('error', 'Invalid option selected.');
    }
    // Limit length to a reasonable size
    if (mb_strlen($field_value) > 30) {
        sendResponse('error', 'Selected option is too long.');
    }
    // Allow only common safe characters (letters, numbers, spaces, underscore, dash, dot, comma)
    if (!preg_match('/^[\p{L}\p{N}\s_\-.,]+$/u', $field_value)) {
        sendResponse('error', 'Invalid characters in selected option.');
    }
    // Normalize/encode to avoid storing raw HTML
    $field_value = htmlspecialchars($field_value, ENT_QUOTES, 'UTF-8');

    try{
        // Start transaction
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO `onboarding_sessions` (onboarding_id, step_number, field_name, field_value, created_at) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("siss", $onboarding_id, $step_number, $field_name, $field_value);

        if ($stmt->execute()) {
            sendResponse('success', 'Step saved');
        } else {
            sendResponse('error', 'Failed to save step');
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}
