<?php
session_start();
// Database connection
require_once __DIR__ . '/../config/databaseconnection.php';

function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        response('error', 'Email and password are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response('error', 'Invalid email format.');
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        response('error', 'Database error: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if user exists
    if ($result->num_rows === 0) {
        response('error', 'Invalid email or password.');
    }
    $user = $result->fetch_assoc();
    // Check if account is deactivated or suspended
    $user_deleted_at = $user['deleted_at'];
    if ($user_deleted_at !== null) {
        response('error', 'Account is deactivated. Please contact support to reactivate your account.');
    }
    $user_suspended_at = $user['suspended_at'];
    if ($user_suspended_at !== null) {
        response('error', 'Account is suspended. Please contact support for more information.');
    }
    //validate user role and auth
    $user_role = $user['role'] ?? '';
    $user_auth = $user['auth'] ?? '';
    if ($user_role === 'administrator' && $user_auth === 'admin') {
        response('error', 'Access Denied. Users only.');
    }
    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session for security
        session_regenerate_id(true);
        unset($user['password']);
        
        // Set session variables
        $_SESSION['user'] = $user;

        response('success', 'Login successful.');
    } else {
        response('error', 'Invalid email or password.');
    }

    $stmt->close();
    $conn->close();
} else {
    response('error', 'Invalid request method.');
}