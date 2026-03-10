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

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('error', 'Invalid request method.');
} else {

    // Admin authentication check
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        response('error', 'Unauthorized');
    }

    // Get data from POST
    $action = $_POST['action'] ?? '';
    $ticketId = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;

    // Validate inputs
    if (empty($action) || $ticketId <= 0) {
        response('error', 'Missing or invalid parameters.');
    }

    $arr_allowed = ['resolve', 'delete', 'open'];
    if(!in_array($action, $arr_allowed, true)){
        response('error', 'Invalid action.');
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

        $allowed_admin = ["admin", "subadmin", "moderator"];
        if (!in_array($admin_type, $allowed_admin, true) || !in_array($admin_auth, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }
        $stmt->close();

        // Fetch ticket
        $stmt = $conn->prepare("SELECT id, user_id, ticket_reference FROM support_ticket WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$stmt) throw new Exception('Database error: ' . $conn->error);
        $stmt->bind_param("i", $ticketId);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$ticket) {
            throw new Exception('Ticket not found.');
        }

        $successMessage = "";
        $sendEmail = false;
        $emailSubject = "";
        $emailMessage = "";
        
        // Determine update query and message
        switch ($action) {
            case 'resolve':
                $updateQuery = "UPDATE support_ticket SET status = 'Resolved', updated_at = NOW() WHERE id = ?";
                $successMessage = "Ticket resolved successfully.";
                $sendEmail = true;
                $ticket_reference = $ticket['ticket_reference'];
                $emailSubject = "Ticket Resolved";
                $emailMessage = "Ticket with reference {$ticket_reference} has been resolved successfully.";
                break;

            case 'open':
                $updateQuery = "UPDATE support_ticket SET status = 'Open', updated_at = NOW() WHERE id = ?";
                $successMessage = "Ticket opened successfully.";
                $sendEmail = true;
                $ticket_reference = $ticket['ticket_reference'];
                $emailSubject = "Ticket Opened";
                $emailMessage = "Ticket with reference {$ticket_reference} has been opened successfully.";
                break;

            case 'delete':
                $updateQuery = "UPDATE support_ticket SET deleted_at = NOW() WHERE id = ?";
                $successMessage = "Ticket deleted successfully.";
                $sendEmail = false;
                break;

        }

        // Execute ticket update
        $stmt = $conn->prepare($updateQuery);
        if (!$stmt) throw new Exception('Database error: ' . $conn->error);
        $stmt->bind_param("i", $ticketId);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        if ($affectedRows <= 0) {
            $conn->rollback();
            response('error', 'No changes made. Ticket might not exist or is already updated.');
        }

        // Commit DB changes
        $conn->commit();
 
        // Send email only for open or resolved tickets
        if ($sendEmail) {
            $stmt = $conn->prepare("SELECT email FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
            if (!$stmt) throw new Exception('Database error: ' . $conn->error);
            $stmt->bind_param("i", $ticket['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user && !empty($user['email'])) {
                $userEmail = $user['email'];

                // Load HTML template
                $body = file_get_contents(__DIR__ . '/mail_template.html');
                if ($body === false) {
                    throw new Exception("Email template not found.");
                }

                // Replace dynamic placeholders
                $body = str_replace('{{subject}}', htmlspecialchars($emailSubject), $body);
                $body = str_replace('{{message}}', nl2br(htmlspecialchars($emailMessage)), $body);
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
                    $mail->addAddress($userEmail);
                    
                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = $emailSubject;
                    $mail->Body    = $body;
                    $mail->AltBody = strip_tags($emailMessage);

                    // Send mail
                    $mail->send();
                    
                } catch (Exception $e) {
                    // Email failed
                    error_log("Email sending failed: " . $e->getMessage());
                }

            }
        }

        response('success', $successMessage);

    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        response('error', 'An internal error occurred. Please try again later.');
    } finally {
        // Close the database connection
        if($stmt !== null){
            $stmt->close();
        }
        $conn->close();
    }
}