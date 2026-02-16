<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

function response($status, $message, $extra = [])
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'extra' => $extra]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin authentication check
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        response('error', 'Unauthorized');
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    // Collect POST data
    $planId = $_POST['plan_type'] ?? null;
    $planName = trim($_POST['plan_name'] ?? '');
    $planPrice = $_POST['plan_price'] ?? null;
    $features = $_POST['features'] ?? [];
    $icons = $_POST['icon_type'] ?? [];

    // Validation
    if (!$planId || !$planName || !$planPrice) {
        response('error', 'Missing required fields.');
    }

    try{
        // Start a transaction
        $conn->begin_transaction();

        //validate authrization
        $stmt = $conn->prepare("SELECT user_type, auth, suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $current_admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            throw new Exception('Administrator not found.');
        }
        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin)) {
            throw new Exception('Action not allowed.');
        }

        if (!in_array($admin_auth, $allowed_admin)) {
            throw new Exception('Action not allowed.');
        }
        
        // Update the subscription plan
        $stmt = $conn->prepare("UPDATE subscription_plans SET name = ?, price = ? WHERE id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("sdi", $planName, $planPrice, $planId);
        $stmt->execute();
        $stmt->close();

        // Remove old features
        $stmt = $conn->prepare("DELETE FROM plan_features WHERE plan_id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $planId);
        $stmt->execute();
        $stmt->close();

        // Insert new features
        if (!empty($features)) {
            $stmt = $conn->prepare("INSERT INTO plan_features (plan_id, feature_text, icon_type, created_at) VALUES (?, ?, ?, NOW())");
            if ($stmt === false) {
                throw new Exception('Database error: ' . $conn->error);
            }
            for ($i = 0; $i < count($features); $i++) {
                $featureText = trim($features[$i]);
                $iconType = $icons[$i] ?? 'check'; // default to 'check'
                if ($featureText !== '') {
                    $stmt->bind_param("iss", $planId, $featureText, $iconType);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }

        // Commit the transaction
        $conn->commit();

        //return success response
        response('success', 'Plan updated successfully.');
    }catch(Exception $e){
        //rollback transaction on error
        $conn->rollback();
        response("error", $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }

} else {
    response('error', 'Invalid request method.');
}