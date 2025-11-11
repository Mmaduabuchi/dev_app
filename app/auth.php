<?php
//strat session
session_start();
//include database connection
require_once __DIR__ . '/../config/databaseconnection.php';

//check if user is logged in
if (!isset($_SESSION['user'])) {
    //if not redirect to login page
    header("Location: /devhire/login");
    exit;
}

$user_id = $_SESSION['user']['id'];

//check if user_id is null
if ($user_id === null) {
    //distroy session
    session_destroy();
    //if null redirect to login page
    header("Location: /devhire/login");
    exit;
}

$user_global_variable = false;

try {

    //check if user is an employer or not
    $stmt = $conn->prepare("SELECT user_type, role FROM `users` WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    // If no user record found
    if (!$user_data) {
        header("Location: /devhire/register");
        exit;
    }

    $user_type = $user_data["user_type"];
    $role = $user_data["role"];

    // Generate CSRF token if not exists
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];

    //Employer/CEO: Allow direct access
    if ($user_type === "employer" && $role === "CEO") {
        // Employer can proceed to dashboard
        $user_global_variable = true;
        return;
    }

    //fetch user data from developers_profiles database
    $stmt = $conn->prepare("SELECT action FROM `developers_profiles` WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // If no profile found, redirect to profile screening page
    if (!$user) {
        header("Location: /devhire/screening");
        exit;
    }

    //check if user has completed profile
    if ($user['action'] === 'step1') {
        //if not redirect to profile completion page
        header("Location: /devhire/screening2");
        exit;
    } else if ($user['action'] === 'step2') {
        //if not redirect to profile completion page
        header("Location: /devhire/screening3");
        exit;
    }
} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}
