<?php
// Database connection
require_once __DIR__ . '/../../../config/databaseconnection.php';

try {
    // Sample data for seeding
    $statuses = ['active', 'expired', 'cancelled', 'pending'];

    // Get all user IDs
    $usersResult = $conn->query("SELECT id FROM `users`");
    $userIds = [];
    while ($row = $usersResult->fetch_assoc()) {
        $userIds[] = $row['id'];
    }

    // Get all plan IDs
    $plansResult = $conn->query("SELECT id, duration_days FROM `subscription_plans`");
    $plans = [];
    while ($row = $plansResult->fetch_assoc()) {
        $plans[] = $row;
    }

    if (empty($userIds) || empty($plans)) {
        throw new Exception("No users or plans found. Make sure users and subscription_plans tables have data.");
    }

    // Seed 20 subscriptions
    for ($i = 0; $i < 20; $i++) {
        // Random user and plan
        $userId = $userIds[array_rand($userIds)];
        $plan = $plans[array_rand($plans)];

        // Random status
        $status = $statuses[array_rand($statuses)];

        // Start date random within last 6 months
        $startDate = date('Y-m-d H:i:s', strtotime('-' . rand(0, 180) . ' days'));

        // End date based on plan duration
        $endDate = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days", strtotime($startDate)));

        // Generate random transaction ID
        $transactionId = strtoupper('TXN-' . bin2hex(random_bytes(5)));

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO `subscriptions` (user_id, plan_id, status, start_date, end_date, transaction_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $stmt->bind_param(
            "iissss",
            $userId,
            $plan['id'],
            $status,
            $startDate,
            $endDate,
            $transactionId
        );

        if (!$stmt->execute()) {
            throw new Exception('Execution error: ' . $stmt->error);
        }
    }

    echo "20 user subscriptions seeded successfully!";
} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}