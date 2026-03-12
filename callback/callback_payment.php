<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//start session
session_start();
// Database connection
require_once __DIR__ . '/../config/databaseconnection.php';

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$paystackSecretKey = $_ENV['PAYSTACK_SECRET_KEY'];

if (!isset($_GET['reference']) || empty($_GET['reference'])) {
    die("No payment reference supplied.");
}

$reference = $_GET['reference'];

// Verify payment with Paystack
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $paystackSecretKey,
        "Cache-Control: no-cache",
    ],
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (!$result || !$result['status']) {
    die("Payment verification failed.");
}

$paymentData = $result['data'];

if ($paymentData['status'] !== 'success') {
    die("Payment not successful.");
}

$userId = $paymentData['metadata']['user_id'];
$planId = $paymentData['metadata']['plan_id'];
$amountPaid = $paymentData['amount'] / 100;

error_log("Paystack Response: " . print_r($paymentData, true));

try {
    // Start DB transaction
    $conn->begin_transaction();

    // Update transaction history
    $stmt = $conn->prepare("UPDATE transaction_history SET status = 'success' WHERE transaction_id = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $stmt->close();

    // Get plan duration
    $stmt = $conn->prepare("SELECT duration_days FROM subscription_plans WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$plan) {
        throw new Exception("Plan not found.");
    }

    $durationDays = (int)$plan['duration_days'];

    $startDate = date('Y-m-d H:i:s');
    $endDate = date('Y-m-d H:i:s', strtotime("+$durationDays days"));

    $status = "active";
    // Insert subscription
    $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date, transaction_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("iissss", $userId, $planId, $status, $startDate, $endDate, $reference);
    $stmt->execute();
    $stmt->close();

    //commit transaction
    $conn->commit();

    // Redirect back to subscription page
    header("Location: /devhire/dashboard/subscriptions?payment=success");
    exit;

} catch (Exception $e) {
    //rollback transaction
    $conn->rollback();
    error_log("Payment callback error: " . $e->getMessage());

    header("Location: /devhire/dashboard/subscriptions?payment=failed");
    exit;
}