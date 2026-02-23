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

    $type = isset($data['type']) ? trim($data['type']) : '';
    $subject = isset($data['subject']) ? trim($data['subject']) : '';
    $message = isset($data['message']) ? trim($data['message']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';

    //validate
    if ($type === '') {
        response('error', 'Type is required');
    }

    $valid_type_arr = ['singleUser', 'employers', 'talents'];
    if (!in_array($type, $valid_type_arr, true)) {
        response('error', 'Invalid type');
    }
    
    //validate single user
    if ($type === 'singleUser') {
        if ($email === '') {
            response('error', 'User email is required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            response('error', 'Invalid email');
        }
    }

    if ($subject === '') {
        response('error', 'Subject is required');
    }

    if (strlen($subject) < 10) {
        response('error', 'Subject must be at least 10 characters long');
    }

    if (strlen($subject) > 100) {
        response('error', 'Subject must not exceed 100 characters');
    }

    //validate message and length
    if ($message === '') {
        response('error', 'Message is required');
    }

    if (strlen($message) < 10) {
        response('error', 'Message must be at least 10 characters');
    }
    if (strlen($message) > 1000) {
        response('error', 'Message must not exceed 1000 characters');
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    try {
        //start transaction
        $conn->begin_transaction();

        //validate authrization
        $stmt = $conn->prepare("SELECT user_type, auth, suspended_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
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
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }

        if (!in_array($admin_auth, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }

        $user_type = '';
        if ($type === 'employers') {
            $user_type = 'employer';
        } elseif ($type === 'talents') {
            $user_type = 'talent';
        } elseif ($type === 'singleUser') {
            $user_type = 'talent';
        }


        $recipients = [];

        if ($type === 'singleUser') {

            $recipients[] = $email;

        } else {

            $stmtUsers = $conn->prepare("SELECT email FROM `users` WHERE user_type = ? AND deleted_at IS NULL AND suspended_at IS NULL");
            if (!$stmtUsers) {
                throw new Exception('Failed to prepare user query.');
            }
            $stmtUsers->bind_param("s", $user_type);
            if (!$stmtUsers->execute()) {
                throw new Exception('Failed to fetch users.');
            }
            $resultUsers = $stmtUsers->get_result();

            while ($row = $resultUsers->fetch_assoc()) {
                if (!empty($row['email']) && filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $row['email'];
                }
            }

            $stmtUsers->close();   
        }

        if (empty($recipients)) {
            throw new Exception('No valid recipients found.');
        }

        $max_per_request = 200;

        if (count($recipients) > $max_per_request) {
            throw new Exception('Too many recipients. Limit is 200 per request.');
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $recipientEmail) {

            $mailSent = false;
            $errorMessage = null;


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
                $mail->addAddress($recipientEmail);
                
                // Email content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->AltBody = strip_tags($message);

                // Send mail
                $mail->send();

                // Set mail sent flag
                $mailSent = true;
                $sentCount++;
                
            } catch (Exception $e) {
                $mailSent = false;
                $errorMessage = $e->getMessage();
                $failedCount++;
            }

            $status = $mailSent ? 'sent' : 'failed';

            $stmtLog = $conn->prepare("INSERT INTO `mail_logs` (admin_id, recipient_email, mail_type, subject, message, status, error_message) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtLog) {
                throw new Exception('Failed to prepare log insert.');
            }
            $stmtLog->bind_param("issssss", $current_admin_id, $recipientEmail, $type, $subject, $message, $status, $errorMessage);
            if (!$stmtLog->execute()) {
                throw new Exception('Failed to insert log.');
            }
            $stmtLog->close();

            // Small delay to avoid spam detection
            usleep(200000); // 0.2 seconds

        }

        $conn->commit();

        response('success', [
            'total' => count($recipients),
            'sent' => $sentCount,
            'failed' => $failedCount
        ]);
        
    } catch (Exception $e) {
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