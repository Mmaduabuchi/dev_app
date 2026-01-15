<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? "";
    $email = $_POST['email'] ?? "";
    $password = $_POST['password'] ?? "";
    $role = $_POST['role'] ?? "";

    //check auth
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        response("error", "Unauthorized access.");
    }

    //validate inputs
    if(!$name || !$email || !$password || !$role){
        response("error", "All fields required.");
    }

    //validate name
    if (strlen($name) < 3) {
        response('error', 'Name must be at least 3 characters long.');
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        response('error', 'Name must contain only letters and spaces.');
    }

    //validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response('error', 'Invalid email format.');
    }
    
    //validate password
    if (strlen($password) < 8) {
        response('error', 'Password must be at least 8 characters long.');
    }

    $role_arr = ["subadmin", "moderator"];
    
    if (!in_array($role, $role_arr)) {
        response('error', 'Invalid administrator role.');
    }

    //get admin id and fullname
    $admin_id = $_SESSION['admin']['id'] ?? null;

    if (!$admin_id || !is_numeric($admin_id)) {
        response('error', 'Invalid session state.');
    }

    try{
        //begin transaction
        $conn->begin_transaction();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }    
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            response('error', 'Email already exists.');
        }
        
        //fetch administrator data
        $stmt = $conn->prepare("SELECT user_type, auth, suspended_at FROM users WHERE id = ? LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }    
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            response('error', 'Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            response('error', 'Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin)) {
            response('error', 'Action not allowed.');
        }

        if (!in_array($admin_auth, $allowed_admin)) {
            response('error', 'Action not allowed.');
        }

        function generateUniqueToken($conn) {
            $token = bin2hex(random_bytes(32));
            $stmt = $conn->prepare("SELECT id FROM users WHERE usertoken = ? LIMIT 1");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0 ? generateUniqueToken($conn) : $token;
        }

        $usertoken = generateUniqueToken($conn);

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $is_profile_complete = 1;
        $default_role = "administrator";
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, usertoken, user_type, role, auth, is_profile_complete, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $usertoken, $role, $default_role, $role, $is_profile_complete);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }
        
        $stmt->close();

        //commit transaction
        $conn->commit();

        response('success', 'New administrator added successfully.');

    } catch (Exception $e) {
        //rollback transaction on error
        $conn->rollback();
        error_log($e->getMessage()); // log internally
        response('error', 'Something went wrong. Please try again.');
    } finally {
        // Close the database connection
        $conn->close();
    }
} else {
    response('error', 'Invalid request method.');
}