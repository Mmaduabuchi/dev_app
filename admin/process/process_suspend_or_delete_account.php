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

    $action = isset($data['action']) ? trim($data['action']) : '';
    $user_id = filter_var($data['user_id'] ?? null, FILTER_VALIDATE_INT);

    $action_arr_data = ["suspend", "delete"];

    if (!in_array($action, $action_arr_data)) {
        response('error', 'Invalid action.');
    }

    if ($user_id === false || $user_id < 1) {
        response('error', 'Invalid user ID.');
    }

    //get admin id
    $current_admin_id = $_SESSION['admin']['id'] ?? null;

    if (!$current_admin_id || !is_numeric($current_admin_id)) {
        response('error', 'Invalid session state.');
    }

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
        $stmt->close();

        $admin_type = $admin['user_type'];
        $admin_auth = $admin['auth'];
        $admin_suspended_at = $admin['suspended_at'];

        if ($admin_suspended_at !== null) {
            throw new Exception('Account is suspended. Action not allowed.');
        }

        $allowed_admin = ["admin", "subadmin"];
        if (!in_array($admin_type, $allowed_admin, true) || !in_array($admin_auth, $allowed_admin, true)) {
            throw new Exception('Action not allowed.');
        }

        if ($user_id == $current_admin_id) {
            throw new Exception('You cannot perform this action on yourself.');
        }

        //get the user account
        $stmt = $conn->prepare("SELECT id, email, user_type, suspended_at, deleted_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('User not found.');
        }

        $user = $result->fetch_assoc();
        //get user email
        $user_email = $user['email'];
        $stmt->close();

        $can_not_delete_user_type = ["admin", "subadmin", "moderator"];
        if (in_array($user['user_type'], $can_not_delete_user_type, true)) {
            throw new Exception('You are not allowed to perform this action on this user.');
        }

        if ($action === "suspend" && $user['suspended_at'] !== null) {
            throw new Exception('User is already suspended.');
        }

        if ($action === "delete" && $user['deleted_at'] !== null) {
            throw new Exception('User is already deleted.');
        }

        if ($action === "suspend") {
            $stmt = $conn->prepare("UPDATE users SET suspended_at = NOW() WHERE id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        }

        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if(!$stmt->execute()) {
            throw new Exception('Database execute failed.');
        }
        $stmt->close();

        //commit transaction
        $conn->commit();

        //send email
        $subject = $action === "suspend" ? "Your account has been suspended." : "Your account has been deleted.";
        $message = $action === "suspend" ? "Your account has been suspended from Our system." : "Your account has been deleted from Our system.";
        
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
            $mail->addAddress($user_email);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($message);

            // Send mail
            $mail->send();
            
        } catch (Exception $e) {
            error_log("Mail Error: " . $e->getMessage());
        }

        $response_message = $action === "suspend"
            ? "User suspended successfully."
            : "User deleted successfully.";

        response('success', $response_message);

    } catch (Exception $e) {
        //rollback transaction on error
        $conn->rollback();
        response("error", $e->getMessage());
    } finally {
        // Close the database connection
        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
        $conn->close();
    }

} else {
    response('error', 'Invalid request method.');
}