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
function sendResponse($status, $message, $redirect = '')
{
    echo json_encode(['status' => $status, 'message' => $message, 'redirect' => $redirect]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    //get data from input
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        sendResponse("error", "Email is required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse('error', 'Invalid email format.');
    }

    if (empty($password)) {
        sendResponse("error", "Password is required.");
    }

    try {

        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        // Check if user exists
        if ($result->num_rows === 0) {
            sendResponse('error', 'Invalid email or password.');
        }
        $admin_details = $result->fetch_assoc();

        $administrator_arr = ["admin", "subadmin", "moderator"];

        //check authority
        if (!in_array($admin_details['user_type'], $administrator_arr) || !in_array($admin_details['auth'], $administrator_arr)) {
            sendResponse('error', 'Access denied. Not an administrator user.');
        }

        // Verify password
        if ($admin_details && password_verify($password, $admin_details['password'])) {

            $otpEnabled = 0;

            $settingQuery = $conn->query("SELECT setting_value FROM `system_settings` WHERE setting_key = 'login_otp_enabled' LIMIT 1");
            if ($settingQuery === false) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $setting = $settingQuery->fetch_assoc();
            if ($setting) {
                $otpEnabled = (int) $setting['setting_value'];
            }

            if ($otpEnabled === 1) {
                //admin is not logged in yet
                $_SESSION['admin_logged_in'] = false;

                $otp = rand(10000, 99999);
                $otpQuery = $conn->prepare("UPDATE `users` SET `login_otp` = ?, `login_otp_expires_at` = NOW() + INTERVAL 5 MINUTE WHERE `id` = ?");
                if ($otpQuery === false) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $otpQuery->bind_param("si", $otp, $admin_details['id']);
                if(!$otpQuery->execute()){
                    throw new Exception('Database error: ' . $conn->error);
                } else {
                    // store in session for verification later
                    $_SESSION['otp_user_id'] = $admin_details['id'];
                    $_SESSION['otp_email'] = $admin_details['email'];
                    $_SESSION['otp_generated'] = $otp;
                    $_SESSION['otp_generated_at'] = time();
                    
                    //mail section
                    $subject =  "Login OTP";

                    $message = "Your login OTP is: {$otp}. It expires in 5 minutes.";

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
                        $mail->addAddress($admin_details['email']);
                        
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
                }
                $otpQuery->close();

                sendResponse('success', 'OTP sent to your email.', '/devhire/admin/log/otp');
            } else {

                // Regenerate session for security
                session_regenerate_id(true);
                unset($admin_details['password']);

                // Set session variables
                $_SESSION['admin'] = $admin_details;
                $_SESSION['admin_logged_in'] = true;
                
                sendResponse('success', 'Login successful.', '/devhire/admin/dashboard/home');
            }

        } else {
            sendResponse('error', 'Invalid email or password.');
        }

        $stmt->close();
    } catch (Exception $e) {
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        if($stmt){
            $stmt->close();
        }
        $conn->close();        
    }
}