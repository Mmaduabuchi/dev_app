<?php
//start session
session_start();
//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//helper function for response
function sendResponse($status, $message, $expires_in = '')
{
    echo json_encode(['status' => $status, 'message' => $message, 'expires_in' => $expires_in]);
    exit;
}


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {

    //get otp from session
    $otp_user_id = $_SESSION['otp_user_id'] ?? null;
    $otp_email = $_SESSION['otp_email'] ?? null;

    if (!$otp_user_id) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    if (!$otp_email) {
        sendResponse('error', 'Session expired. Please login again.');
    }

    try {
        //start transaction
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT id, email FROM `users` WHERE id = ? LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $stmt->bind_param("i", $otp_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin_details = $result->fetch_assoc();

        //generate new otp
        $new_otp = rand(10000, 99999);

        //update user login otp
        $stmt = $conn->prepare("UPDATE users SET login_otp = ?, login_otp_expires_at = NOW() + INTERVAL 5 MINUTE WHERE id = ?");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $new_otp, $otp_user_id);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $stmt->close();

        //mail
        $subject =  "Login OTP";

        $message = "Your login OTP is: {$new_otp}. It expires in 5 minutes.";

        // Load HTML template
        $body = file_get_contents(__DIR__ . '/mail_template.html');

        if ($body === false) {
            throw new Exception("Email template not found.");
        }

        // Replace dynamic placeholders
        $body = str_replace('{{subject}}', htmlspecialchars($subject), $body);
        $body = str_replace('{{message}}', nl2br(htmlspecialchars($message)), $body);
        $body = str_replace('{{year}}', date('Y'), $body);

        //send otp to user
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
            $mail->addAddress($admin_details['email']);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($message);

            // Send mail
            $mail->send();
            
        } catch (Exception $e) {
            throw new Exception('Email sending failed: ' . $e->getMessage());
        }

        //commit transaction
        $conn->commit();

        $expiresIn = 5 * 60;

        sendResponse('success', 'OTP sent successfully.', $expiresIn);

    } catch (Exception $e) {
        //rollback transaction
        $conn->rollback();
        sendResponse('error', 'Failed to send OTP: ' . $e->getMessage());
    } finally {
        if ($stmt) {
            $stmt->close();
        }
        $conn->close();
    }

}