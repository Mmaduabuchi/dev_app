<?php
require_once __DIR__ . '/config/databaseconnection.php';
//check if user is logged in
if (!isset($_SESSION['user'])) {
    //if not redirect to dashboard page
    header("Location: /devhire/login");
    exit;
} else {
    //get user id from session
    $user_id = $_SESSION['user']['id'] ?? null;
    
    //get user data from session
    $stmt = $conn->prepare("SELECT action FROM developers_profiles WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();

    if ($result->num_rows > 0) {
        //if action is step1 redirect to screening2 page
        if ($userData['action'] === 'step1') {
            header("Location: /devhire/screening2");
            exit;
        }elseif ($userData['action'] === 'completed') {
            //if action is completed redirect to dashboard page
            header("Location: /devhire/dashboard");
            exit;
        }
    } else {
        //if no profile found redirect to screening page
        header("Location: /devhire/screening");
        exit;
    }
}
