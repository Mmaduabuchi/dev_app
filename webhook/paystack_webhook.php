<?php
require_once __DIR__ . '/../config/databaseconnection.php';

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Paystack secret key
$paystackSecret = $_ENV['PAYSTACK_SECRET_KEY'];

// Get payload
$payload = @file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

// Verify signature
$hash = hash_hmac('sha512', $payload, $paystackSecret);
if ($hash !== $signature) {
    http_response_code(400);
    exit('Invalid signature');
}

// Decode payload
$data = json_decode($payload, true);
if (!$data) {
    http_response_code(400);
    exit('Invalid payload');
}

// Process transaction
$event = $data['event'] ?? '';
if ($event === 'charge.success') {
    $ref = $data['data']['reference'];
    $amount = $data['data']['amount'] / 100;
    $planId = $data['data']['metadata']['plan_id'];
    $userId = $data['data']['metadata']['user_id'];
    $paymentType = $data['data']['metadata']['payment_type'] ?? 'new';

    // Update transaction and subscription in DB...
    try {
        // Begin transaction
        $conn->begin_transaction();

        // Update transaction status
        $stmt = $conn->prepare("UPDATE transaction_history SET status='success', updated_at=NOW() WHERE transaction_id=?");
        $stmt->bind_param("s", $ref);
        $stmt->execute();
        $stmt->close();

        // Get plan duration
        $stmt = $conn->prepare("SELECT duration_days FROM subscription_plans WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $planId);
        $stmt->execute();
        $plan = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));

        // If upgrade or downgrade, cancel existing active subscriptions
        if (in_array($paymentType, ['upgrade', 'downgrade'])) {
            $cancelStmt = $conn->prepare("UPDATE subscriptions SET status = 'cancelled', cancelled_at = NOW() WHERE user_id = ? AND status = 'active'");
            $cancelStmt->bind_param("i", $userId);
            $cancelStmt->execute();
            $cancelStmt->close();
        }

        $status = "active";
        // Add subscription
        $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan_id, status, start_date, end_date, transaction_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissss", $userId, $planId, $status, $startDate, $endDate, $ref);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

    } catch(Exception $e) {
        $conn->rollback();
        error_log("Webhook error: ".$e->getMessage());
        http_response_code(500);
        exit('Internal error');
    }
}

// Respond 200 OK to Paystack
http_response_code(200);
echo 'OK';