<?php
// Database connection
require_once __DIR__ . '/../../../config/databaseconnection.php';

// Function to generate random text
function randomText($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyz ';
    $text = '';
    for ($i = 0; $i < $length; $i++) {
        $text .= $characters[rand(0, strlen($characters) - 1)];
    }
    return ucfirst(trim($text));
}


// Function to generate unique ticket reference
function generateTicketRef($index) {
    $date = date('Ymd'); // e.g., 20251110
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); // random 6 chars
    return "TCK-$date-$random-$index";
}

// Define possible categories and statuses
$categories = ['Technical Support', 'Billing', 'Account Issue', 'Feedback', 'General Inquiry'];
$statuses = ['Open', 'In Progress', 'Closed', 'Resolved', 'Pending'];

try {
    for ($i = 1; $i <= 10; $i++) {
        $user_id = rand(1, 5);
        $ticket_reference = generateTicketRef($i);
        $title = 'Issue #' . $i . ': ' . randomText(rand(5, 12));
        $category = $categories[array_rand($categories)];
        $message = 'This is a sample support message about ' . strtolower($category) . '. ' . randomText(rand(40, 80));
        $status = $statuses[array_rand($statuses)];
        $created_at = date('Y-m-d H:i:s', strtotime("-" . rand(0, 30) . " days"));
        $deleted_at = (rand(0, 10) > 8) ? date('Y-m-d H:i:s', strtotime("-" . rand(1, 15) . " days")) : NULL; // randomly deleted

        $stmt = $conn->prepare("INSERT INTO support_ticket (user_id, ticket_reference, title, category, message, status, created_at, deleted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $ticket_reference, $title, $category, $message, $status, $created_at, $deleted_at);
        $stmt->execute();
    }

    echo "10 fake support tickets inserted successfully!";
} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}