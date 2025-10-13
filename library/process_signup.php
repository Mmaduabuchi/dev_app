<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

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
    $role = $_POST['role'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    // Basic validation
    if (empty($role) || empty($fullname) || empty($email) || empty($password) || empty($confirmpassword)) {
        response('error', 'All fields are required.');
    }
    //validate fullname (only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        response('error', 'Full name can only contain letters and spaces.');
    }
    //validate role (only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $role)) {
        response('error', 'Role can only contain letters and spaces.');
    }
    //validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response('error', 'Invalid email format.');
    }
    // Check if passwords match and meet criteria
    if ($password !== $confirmpassword) {
        response('error', 'Passwords do not match.');
    }
    if (strlen($password) < 6) {
        response('error', 'Password must be at least 6 characters long.');
    }
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        response('error', 'Database error: ' . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        response('error', 'Email is already registered.');
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Generate unique usertoken
    do {    
        $user_token = bin2hex(random_bytes(32));
        $check = $conn->prepare("SELECT id FROM users WHERE usertoken = ? LIMIT 1");
        $check->bind_param("s", $user_token);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();
    } while ($exists);

    // Current timestamp
    $created_at = date('Y-m-d H:i:s');
    // default for new users
    $is_profile_complete = 0;
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (google_id, picture, fullname, email, password, usertoken, user_type, role, auth, is_profile_complete, suspended_at, created_at, updated_at, deleted_at) VALUES (NULL, NULL, ?, ?, ?, ?, 'talent', ?, 'user', ?, NULL, ?, ?, NULL)");
    if (!$stmt) {
        response('error', 'Database error: ' . $conn->error);
    }
    $stmt->bind_param("sssssiss", $fullname, $email, $hashed_password, $user_token, $role, $is_profile_complete, $created_at, $created_at);

    if ($stmt->execute()) {
        //Fetch the newly created user
        $new_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $new_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        unset($user['password']);
        //Store in session
        $_SESSION['user'] = $user;

        // Respond with success
        response('success', 'Registration successful. You can now log in.');
    } else {
        response('error', 'Registration failed. Please try again.');
    }

    $stmt->close();
    $conn->close();
} else {
    response('error', 'Invalid request method.');
}