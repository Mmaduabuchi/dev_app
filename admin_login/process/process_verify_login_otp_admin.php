<?php
//start session
session_start();
//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

//helper function for response
function sendResponse($status, $message, $redirect = '')
{
    echo json_encode(['status' => $status, 'message' => $message, 'redirect' => $redirect]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    $data = json_decode(file_get_contents('php://input'), true);

    //get data from input
    $otp = $data['otp'] ?? null;

    //validate otp
    if (!$otp) {
        sendResponse('error', 'OTP is required.');
    }

    //get otp from session
    $otp_user_id = $_SESSION['otp_user_id'] ?? null;
    $otp_email = $_SESSION['otp_email'] ?? null;
    $otp_generated = $_SESSION['otp_generated'] ?? null;
    $otp_generated_at = $_SESSION['otp_generated_at'] ?? null;

    if (!$otp_user_id) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    if (!$otp_email) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    if (!$otp_generated) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    if (!$otp_generated_at) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    if (!is_numeric($otp)) {
        sendResponse('error', 'Invalid OTP.');
    }

    try {
        //start transaction
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        } 

        $stmt->bind_param("i", $otp_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin_details = $result->fetch_assoc();

        $admin_login_otp = $admin_details['login_otp'];
        $admin_login_otp_expires_at = $admin_details['login_otp_expires_at'];

        //validate otp
        if ($otp != $admin_login_otp) {
            throw new Exception('Invalid OTP.');
        }

        //validate otp expires at
        if (time() > strtotime($admin_login_otp_expires_at)) {
            throw new Exception('OTP has expired.');
        }
        $stmt->close();

        //update user login otp
        $stmt = $conn->prepare("UPDATE users SET login_otp = null, login_otp_expires_at = null WHERE id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $otp_user_id);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $stmt->close();

        //remove otp from session
        unset($_SESSION['otp_user_id']);
        unset($_SESSION['otp_email']);
        unset($_SESSION['otp_generated']);
        unset($_SESSION['otp_generated_at']);

        // Regenerate session for security
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['admin'] = $admin_details;
        $_SESSION['admin_logged_in'] = true;

        //commit transaction
        $conn->commit();

        sendResponse('success', 'Login successful.', '/devhire/admin/dashboard');

    } catch (Exception $e) {
        //rollback transaction
        $conn->rollback();
        sendResponse('error', 'Failed to verify OTP: ' . $e->getMessage());
    } finally {
        //close connection
        if ($stmt) {
            $stmt->close();
        }
        $conn->close();
    }

}