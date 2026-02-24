<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin authentication check
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        response('error', 'Unauthorized');
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $otp_enabled = isset($data['otp_enabled']) ? $data['otp_enabled'] : null;

    if ($otp_enabled === null) {
        response('error', 'OTP enabled is required.');
    }

    // Strict validation (ONLY 0 or 1 allowed)
    $otp_enabled = filter_var($otp_enabled, FILTER_VALIDATE_INT);

    if ($otp_enabled !== 0 && $otp_enabled !== 1) {
        response('error', 'Invalid OTP value.');
    }
    
    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    if (!$current_admin_id || !is_numeric($current_admin_id)) {
        response('error', 'Invalid session state.');
    }

    $stmt = null;
    try {
        //start transaction
        $conn->begin_transaction();

        //validate authrization
        $stmt = $conn->prepare("SELECT email, user_type, auth, suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $current_admin_id);
        if (!$stmt->execute()) {
            throw new Exception('Database execute failed.');
        }
        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            throw new Exception('Administrator not found.');
        }

        $admin = $result->fetch_assoc();
        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_email = $admin['email'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin, true) || !in_array($admin_auth, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }

        $stmt->close();

        // Check if setting exists
        $check = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = 'login_otp_enabled' LIMIT 1");
        $check->execute();
        $checkResult = $check->get_result();
        $check->close();

        if ($checkResult->num_rows === 0) {
            // Insert if not exists
            $insert = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('login_otp_enabled', ?)");
            $insert->bind_param("i", $otp_enabled);
            $insert->execute();
            $insert->close();
        } else {
            // Update if exists
            $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'login_otp_enabled'");
            $stmt->bind_param("i", $otp_enabled);
            $stmt->execute();
            $stmt->close();
        }
 

        $conn->commit();


        $subject = $otp_enabled === 1 ? "Login OTP Enabled" : "Login OTP Disabled";

        $message = $otp_enabled === 1
            ? "Login OTP has been enabled successfully. All Administrators and moderators will now be required to verify OTP during login."
            : "Login OTP has been disabled successfully. Administrators, Sub Administrators and moderators can now login without OTP verification.";

        // Load HTML template
        $body = file_get_contents(__DIR__ . '/mail_template.html');

        if ($body === false) {
            throw new Exception("Email template not found.");
        }

        // Replace dynamic placeholders
        $body = str_replace('{{subject}}', htmlspecialchars($subject), $body);
        $body = str_replace('{{message}}', nl2br(htmlspecialchars($message)), $body);
        $body = str_replace('{{year}}', date('Y'), $body);

        try {
            $mail = new PHPMailer(true);

            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port         = 587;

            // Sender & recipient
            $mail->setFrom('noreply@devhire.com', 'DevHire Notifications');
            $mail->addAddress($admin_email);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($message);

            // Send mail
            $mail->send();
            
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        response('success', 'OTP setting updated successfully.');
    } catch (Exception $e) {
        $conn->rollback();
        response('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        if($stmt !== null){
            $stmt->close();
        }
        $conn->close();
    }

} else {
    response('error', 'Invalid request method.');
}