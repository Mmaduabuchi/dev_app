<?php
session_start();
require_once __DIR__ . '/../config/databaseconnection.php';

function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        response('error', 'Invalid request payload.');
    }

    $token = $data['token'] ?? '';
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';

    if (empty($token) || empty($password) || empty($confirmPassword)) {
        response('error', 'All fields are required.');
    }

    if ($password !== $confirmPassword) {
        response('error', 'Passwords do not match.');
    }

    if (strlen($password) < 6) {
        response('error', 'Password must be at least 6 characters long.');
    }

    // Find the reset request
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();
    $stmt->close();

    if (!$reset) {
        response('error', 'Invalid or expired token.');
    }

    // Check expiration
    if (strtotime($reset['expires_at']) < time()) {
        response('error', 'Token has expired. Please request a new reset.');
    }

    $email = $reset['email'];
    $user_id = $reset['user_id'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update user’s password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND id = ?");
    $stmt->bind_param("ssi", $hashedPassword, $email, $user_id);

    if ($stmt->execute()) {
        // Delete the token after use
        $del = $conn->prepare("DELETE FROM password_resets WHERE email = ? AND user_id = ?");
        $del->bind_param("si", $email, $user_id);
        $del->execute();
        $del->close();

        $stmt->close();
        response('success', 'Password has been reset successfully.');
    } else {
        response('error', 'Failed to reset password. Please try again.');
    }
} else {
    response('error', 'Invalid request method.');
}