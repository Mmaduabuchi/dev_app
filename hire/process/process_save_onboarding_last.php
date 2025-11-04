<?php
//start session
session_start();
//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

//helper function for response
function sendResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['onboarding_id'])) {
    sendResponse('error', 'Invalid user token.');
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    $onboarding_id = $_SESSION['onboarding_id'];
    $CompanyEmail = $_POST['CompanyEmail'] ?? '';
    $CompanyName = $_POST['CompanyName'] ?? '';
    $CompanyContactName = $_POST['CompanyContactName'] ?? '';
    $CompanyPhoneNumber = $_POST['CompanyPhoneNumber'] ?? '';
    $Password = $_POST['Password'] ?? '';
    $ConfirmPassword = $_POST['ConfirmPassword'] ?? '';
    $step_number = (int)$_POST['step_number'] ?? '';
    // $field_name = 'contacts';

    //page number data
    $data = 7;

    //validate session
    if (!$onboarding_id) {
        sendResponse('error', 'Invalid user session id.');
    }

    //validate step number
    if ($step_number !== $data) {
        sendResponse('error', 'Invalid onboarding stage access denied.');
    }

    if (empty($CompanyEmail) || empty($CompanyName) || empty($CompanyContactName) || empty($CompanyPhoneNumber) || empty($Password) || empty($ConfirmPassword)) {
        sendResponse('error', 'All input fields are requried');
    }

    if (!is_string($CompanyName) || !is_string($CompanyContactName)) {
        sendResponse('error', 'Invalid name Format.');
    }
    // Limit length to a reasonable size
    if (mb_strlen($CompanyName) > 30) {
        sendResponse('error', 'Company name is too long.');
    }
    if (mb_strlen($CompanyContactName) > 30) {
        sendResponse('error', 'Contact name is too long.');
    }

    // Allow only common safe characters (letters, numbers, spaces, underscore, dash, dot, comma)
    if (!preg_match('/^[\p{L}\p{N}\s_\-.,]+$/u', $CompanyName)) {
        sendResponse('error', 'Invalid characters in company name.');
    }

    //validate CompanyContactName (only letters and spaces)
    if (!preg_match("/^[a-zA-Z\s]+$/", $CompanyContactName)) {
        sendResponse('error', 'Contact name can only contain letters and spaces.');
    }

    //validate email format
    if (!filter_var($CompanyEmail, FILTER_VALIDATE_EMAIL)) {
        sendResponse('error', 'Invalid company email format.');
    }

    //validate password
    if ($Password !== $ConfirmPassword) {
        sendResponse('error', 'Password do not match.');
    }

    // check password length
    if (strlen($Password) < 8) {
        sendResponse('error', 'Password must be at least 8 characters long.');
    }

    // check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $Password)) {
        sendResponse('error', 'Password must contain at least one uppercase letter.');
    }

    // check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $Password)) {
        sendResponse('error', 'Password must contain at least one lowercase letter.');
    }

    // check for at least one number
    if (!preg_match('/[0-9]/', $Password)) {
        sendResponse('error', 'Password must contain at least one number.');
    }

    // check for at least one special character
    if (!preg_match('/[\W_]/', $Password)) {
        sendResponse('error', 'Password must contain at least one special character (e.g. @, #, $, %, &).');
    }

    // Normalize/encode to avoid storing raw HTML
    $CompanyName = htmlspecialchars($CompanyName, ENT_QUOTES, 'UTF-8');
    $CompanyContactName = htmlspecialchars($CompanyContactName, ENT_QUOTES, 'UTF-8');


    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $CompanyEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            sendResponse('error', 'Email is already registered.');
        }
        $stmt->close();

        // Check if onboarding_id already exists
        $stmt = $conn->prepare("SELECT id FROM `onboarding_to_users` WHERE onboarding_id = ? LIMIT 1");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }
        $stmt->bind_param("s", $onboarding_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            unset($_SESSION['onboarding_id']);
            sendResponse('error', 'Already registered or suspended account.');
        }
        $stmt->close();

        // Hash the password
        $hashed_password = password_hash($Password, PASSWORD_DEFAULT);
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
        //user role
        $role = 'CEO';

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (google_id, picture, fullname, email, password, usertoken, tel, user_type, role, auth, is_profile_complete, suspended_at, created_at, updated_at, deleted_at) VALUES (NULL, NULL, ?, ?, ?, ?, ?, 'employer', ?, 'user', ?, NULL, ?, ?, NULL)");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }
        $stmt->bind_param("ssssssiss", $CompanyContactName, $CompanyEmail, $hashed_password, $user_token, $CompanyPhoneNumber, $role, $is_profile_complete, $created_at, $created_at);

        if (!$stmt->execute()) {
            sendResponse('error', 'Registration failed. Please try again.');
        }

        //Fetch the newly created user
        $new_id = $stmt->insert_id;
        $stmt->close();

        //insert record to onboarding_to_users
        $stmt = $conn->prepare("INSERT INTO `onboarding_to_users` (user_id, onboarding_id, created_at) VALUES (?, ?, NOW())");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }
        $stmt->bind_param("is", $new_id, $onboarding_id);
        if (!$stmt->execute()) {
            sendResponse('error', 'Registration failed. Please check your internet connection and try again later.');
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        if (!$stmt) {
            sendResponse('error', 'Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $new_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        unset($user['password']);
        //Store in session
        $_SESSION['user'] = $user;

        // Commit transaction
        $conn->commit();
        
        // Respond with success
        sendResponse('success', 'Registration successful.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}