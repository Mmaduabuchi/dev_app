<?php
session_start();
// Database connection
require_once __DIR__ . '/../config/databaseconnection.php';
// load PHPMailer
require_once __DIR__ . '/../vendor/autoload.php'; 
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
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
    // Try to get input from JSON first
    $rawInput = file_get_contents("php://input");

    $data = json_decode($rawInput, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($data['email'])) {
        $email = $data['email'];
    } else {
        // Fallback to normal form-data POST
        $email = $_POST['email'] ?? '';
    }

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Basic validation
    if (empty($email)) {
        response('error', 'Email is required.');
    }
    //validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response('error', 'Invalid email format.');
    }
    // Check if email exists
    $stmt = $conn->prepare("SELECT id, fullname FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        response('error', 'Database error: ' . $conn->error);
    }   
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($result->num_rows === 0) {
        response('error', 'Email not found.');
    }
    $stmt->close();

    //get user id
    $user_id = $user['id'];

    do {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(32));

        // Check if it already exists in password_resets
        $check = $conn->prepare("SELECT id FROM password_resets WHERE token = ? LIMIT 1");
        $check->bind_param("s", $token);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

    } while ($exists); // keep regenerating until it’s unique
    
    // Set token expiration (1 hour from now)
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Save token in DB (you need a `password_resets` table)
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $email, $token, $expires);
    $stmt->execute();
    $stmt->close();

    // Create reset link
    $resetLink = "http://localhost/devhire/reset?token=" . $token;

    // Send email with PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Sender & recipient
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'DevHire Support');
        $mail->addAddress($email, $user['fullname']);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
            <p>Hi <b>{$user['fullname']}</b>,</p>
            <p>You requested a password reset. Click the link below to reset your password:</p>
            <p><a href='{$resetLink}'>{$resetLink}</a></p>
            <p>This link will expire in 1 hour.</p>
        ";

        $mail->send();
        response('success', 'Password reset email sent successfully.');
    } catch (Exception $e) {
        response('error', 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
} else {
    response('error', 'Invalid request method.');
}