<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

// Validate GET
if (
    !isset($_GET['user_id']) || 
    !isset($_GET['token_ref']) ||
    !is_numeric($_GET['user_id']) ||
    strlen($_GET['token_ref']) !== 72 ||
    !ctype_xdigit($_GET['token_ref'])
) {
    die("Invalid request.");
}

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Unauthorized access.');
}

$user_id = (int) $_GET['user_id'];

try {

    // Fetch transactions
    $stmt = $conn->prepare("SELECT * FROM transaction_history WHERE user_id = ? ORDER BY created_at DESC");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    // Clean output buffer
    if (ob_get_length()) {
        ob_end_clean();
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transaction_log_user_'.$user_id.'.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen("php://output", "w");

    // CSV Column headers
    fputcsv($output, [
        'Transaction ID',
        'Plan',
        'Method',
        'Amount',
        'Status',
        'Date'
    ]);   

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['transaction_id'],
                $row['plan'],
                ucfirst($row['method']),
                number_format($row['amount'], 2),
                ucfirst($row['status']),
                date("M d, Y", strtotime($row['created_at']))
            ]);
        }
    } else {
        // show empty row if no records
        fputcsv($output, ['No transactions found']);
    }

    fclose($output);
    exit;
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    // Close the database connection
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    $conn->close();
}