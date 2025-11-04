<?php
// Database connection
require_once __DIR__ . '/../../../config/databaseconnection.php';

try {
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO `notifications` (user_id, sender_id, sender_email, title, message, type, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'unread', NOW())");

    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    // Example seed data
    $titles = [
        "New Job Match Found",
        "Project Invitation",
        "Interview Scheduled",
        "Account Verified",
        "Message from Employer",
        "Task Reminder",
        "New Applicant Alert",
        "Payment Processed",
        "Profile Viewed",
        "Job Application Received"
    ];

    // Loop to insert 10 sample notifications
    for ($i = 1; $i <= 10; $i++) {
        $user_id = rand(1, 5);
        $sender_id = rand(1, 5);
        $sender_email = "sender{$i}@example.com";
        $title = $titles[$i - 1];
        $message = "This is a sample message for notification {$i}.";
        $type = "info"; // You can adjust type e.g. info, alert, system, etc.

        // Bind and execute
        $stmt->bind_param("iissss", $user_id, $sender_id, $sender_email, $title, $message, $type);
        $stmt->execute();
    }

    echo "Seeder executed successfully. 10 notifications added.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();