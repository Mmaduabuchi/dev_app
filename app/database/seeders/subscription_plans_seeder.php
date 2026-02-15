<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Database connection
require_once __DIR__ . '/../../../config/databaseconnection.php';

try {
    // Array of sample subscription plans
    $plans = [
        [
            'name' => 'Free',
            'price' => 0.00,
            'duration_days' => 30,
            'description' => 'Access to basic features, Email support'
        ],
        [
            'name' => 'Standard',
            'price' => 9.00,
            'duration_days' => 90,
            'description' => 'All Basic features + Priority support, Additional reports'
        ],
        [
            'name' => 'Premium',
            'price' => 15.00,
            'duration_days' => 365,
            'description' => 'All Standard features + Dedicated account manager, Advanced analytics'
        ]
    ];

    foreach ($plans as $plan) {
        $stmt = $conn->prepare("INSERT INTO `subscription_plans` (name, price, duration_days, description, created_at)  VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("sdis",$plan['name'],$plan['price'], $plan['duration_days'], $plan['description']);
        if (!$stmt->execute()) {
            throw new Exception('Execution error: ' . $stmt->error);
        }
    }

    echo "Subscription plans seeded successfully!";
} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}