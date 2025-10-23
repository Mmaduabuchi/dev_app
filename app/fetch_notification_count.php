<?php
//fecth notification count for the logged in user
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id']; 
    $stmt = $conn->prepare("SELECT COUNT(*) as notification_count FROM notifications WHERE user_id = ? AND status = 'unread' AND deleted_at IS NULL");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    //response with notification count
    $notification_count =  $data['notification_count'];

    //close stmt
    $stmt->close();
}