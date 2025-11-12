<?php
//include database connection
require_once __DIR__ . '/../config/databaseconnection.php';

//get user id
$userID = $_SESSION['user']['id'];

try{

    $stmt = $conn->prepare("SELECT * FROM `subscriptions` WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    if(!$stmt){
        throw new Exception("Database error" . $conn->error);
    }
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows < 1){
        header("location: /devhire/dashboard/subscriptions");
        exit();
    }

    $userSubscription = $result->fetch_assoc();

    //check subscription status
    if($userSubscription['status'] === 'expired' || $userSubscription['status'] === 'cancelled'){
        header("location: /devhire/dashboard/change-plan");
        exit();
    }

    $stmt->close();


} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}