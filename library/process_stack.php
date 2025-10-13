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
    $role = $_POST['role'] ?? '';

    if (empty($role)) {
        response('error', 'Role is required.');
    }

    // Validate role
    $valid_roles = ['Developer', 'Designer', 'Management Consultant', 'Project Manager', 'Marketing Expert', 'Product Manager'];
    if (!in_array($role, $valid_roles)) {
        response('error', 'Invalid role selected.');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        response('error', 'User not logged in.');
    }

    $user_id = $_SESSION['user']['id'];

    // Update user's role in the database
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    if (!$stmt) {
        response('error', 'Database error: ' . $conn->error);
    }
    $stmt->bind_param("si", $role, $user_id);
    
    if ($stmt->execute()) {
        // Update session data
        $_SESSION['user']['role'] = $role;
        response('success', 'Role updated successfully.');
    } else {
        response('error', 'Failed to update role: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    response('error', 'Invalid request method.');
}