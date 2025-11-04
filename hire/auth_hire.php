<?php
//start session
session_start();

//authenticate user
if (!isset($_SESSION['onboarding_id'])) {
    header("location: /devhire/");
    exit;
}

//get user onboarding_id token
$onboarding_id = $_SESSION['onboarding_id'];

try {
    //validate user onboarding_id token
    $stmt = $conn->prepare("SELECT id FROM `onboarding_to_users` WHERE onboarding_id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("s", $onboarding_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("location: /devhire/");
        exit;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}