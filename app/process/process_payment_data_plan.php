<?php
// Start session
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Helper function
function sendResponse($status, $message, $extra = [])
{
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

// Fallback for getallheaders() if not available (e.g. nginx/CGI)
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {

    // Decode JSON body
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? null;

    // CSRF check
    $headers = getallheaders();
    $csrfToken = $headers['X-Csrf-Token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || empty($csrfToken) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        sendResponse('error', 'Invalid CSRF token. Request denied.');
    }

    // Validate action
    if ($action !== 'initiate_plan_payment') {
        sendResponse('error', 'Invalid action.');
    }

    // Must be logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    $userId = (int) $_SESSION['user']['id'];
    $usertoken = $_SESSION['user']['usertoken'] ?? null;

    if (!$usertoken) {
        sendResponse('error', 'Invalid session token. Please log in again.');
    }

    // Validate plan_id
    $planId = isset($data['plan_id']) ? (int) $data['plan_id'] : 0;
    if ($planId <= 0) {
        sendResponse('error', 'Invalid plan selected.');
    }
 
    $paymentType = isset($data['payment_type']) ? $data['payment_type'] : 'new';
    if (!in_array($paymentType, ['new', 'upgrade', 'downgrade'])) {
        $paymentType = 'new';
    }

    // Never let them "choose" plan 1 (the free/active plan)
    if ($planId === 1) {
        sendResponse('error', 'This plan is already active.');
    }

    // Paystack secret key and callback url
    $paystackSecretKey = $_ENV['PAYSTACK_SECRET_KEY'];
    $callbackUrl = $_ENV['PAYSTACK_CALLBACK_URL'];
    
    try {
        $conn->begin_transaction();

        // Verify user exists and is not suspended
        $stmt = $conn->prepare("SELECT usertoken, suspended_at, email, fullname FROM `users` WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$stmt) throw new Exception('DB error: ' . $conn->error);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$userRow) {
            sendResponse('error', 'User account not found.');
        }
        if ($userRow['suspended_at'] !== null) {
            sendResponse('error', 'Your account has been suspended. Please contact support.');
        }
        if ($userRow['usertoken'] !== $usertoken) {
            sendResponse('error', 'Session mismatch. Please log in again.');
        }

        $userEmail = $userRow['email'];
        $userFullname = $userRow['fullname'] ?? 'User';

        // Fetch the selected subscription plan
        $stmt = $conn->prepare("SELECT id, name, price, duration_days FROM `subscription_plans` WHERE id = ? LIMIT 1");
        if (!$stmt) throw new Exception('DB error: ' . $conn->error);
        $stmt->bind_param("i", $planId);
        $stmt->execute();
        $plan = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$plan) {
            sendResponse('error', 'Selected plan not found.');
        }

        $planName  = ucfirst($plan['name']);
        $planPrice = (float) $plan['price'];

        // Amount in kobo (Paystack uses smallest currency unit for NGN)
        // Adjust multiplier based on your currency. For NGN: * 100, for USD: * 100
        $amountInKobo = (int) round($planPrice * 100);

        // Generate a unique transaction reference
        do {
            $transactionRef = 'DH-' . strtoupper(bin2hex(random_bytes(8)));
            $chk = $conn->prepare("SELECT id FROM transaction_history WHERE transaction_id = ? LIMIT 1");
            if (!$chk) throw new Exception('DB error: ' . $conn->error);
            $chk->bind_param("s", $transactionRef);
            $chk->execute();
            $refExists = $chk->get_result()->num_rows > 0;
            $chk->close();
        } while ($refExists);

        // Determine user_company (employer company name or user fullname)
        $stmtComp = $conn->prepare("SELECT company_name FROM employer_profiles WHERE user_id = ? LIMIT 1");
        $userCompany = $userFullname; // fallback
        if ($stmtComp) {
            $stmtComp->bind_param("i", $userId);
            $stmtComp->execute();
            $compRow = $stmtComp->get_result()->fetch_assoc();
            $stmtComp->close();
            if ($compRow && !empty($compRow['company_name'])) {
                $userCompany = $compRow['company_name'];
            }
        }

        // Insert pending transaction into transaction_history
        $method = 'paystack';
        $status = 'pending';
        $stmt = $conn->prepare(
            "INSERT INTO transaction_history 
                (transaction_id, user_id, user_company, plan, amount, method, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        if (!$stmt) throw new Exception('DB error: ' . $conn->error);
        $stmt->bind_param("sissdss", $transactionRef, $userId, $userCompany, $planName, $planPrice, $method, $status);
        $stmt->execute();
        if ($stmt->affected_rows < 1) {
            throw new Exception('Failed to record payment data. Please try again.');
        }
        $stmt->close();

        $conn->commit();

        // Initialize Paystack transaction
        $paystackPayload = json_encode([
            'email' => $userEmail,
            'amount' => $amountInKobo,
            'reference'=> $transactionRef,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'user_id' => $userId,
                'plan_id' => $planId,
                'plan_name' => $planName,
                'payment_type' => $paymentType
            ]
        ]);

        $ch = curl_init('https://api.paystack.co/transaction/initialize');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $paystackPayload,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $paystackSecretKey,
                'Content-Type: application/json',
                'Cache-Control: no-cache',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $paystackResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception('Payment gateway connection failed: ' . $curlError);
        }

        $paystackData = json_decode($paystackResponse, true);

        if (!$paystackData || !isset($paystackData['status']) || $paystackData['status'] !== true) {
            $errMsg = $paystackData['message'] ?? 'Paystack initialization failed. Please try again.';
            throw new Exception($errMsg);
        }

        $authorizationUrl = $paystackData['data']['authorization_url'] ?? null;
        if (!$authorizationUrl) {
            throw new Exception('No payment URL returned from Paystack.');
        }

        // Success — return the payment URL to front-end
        sendResponse('success', 'Payment initiated successfully.', [
            'payment_url' => $authorizationUrl,
            'reference' => $transactionRef,
        ]);

    } catch (Exception $e) {
        if ($conn->in_transaction) {
            $conn->rollback();
        }
        error_log('[process_payment_data_plan] ' . $e->getMessage());
        sendResponse('error', $e->getMessage());
    } finally {
        if (isset($conn) && $conn instanceof mysqli) {
            $conn->close();
        }
    }
}