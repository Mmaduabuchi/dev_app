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

//fetch user data from database
$stmt = $conn->prepare("SELECT action FROM developers_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


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
}else if ($user['action'] === 'step2') {
    //if not redirect to profile completion page
    header("Location: /devhire/screening3");
    exit;
}