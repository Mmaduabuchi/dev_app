<?php
session_start();
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Validate
if (
    !isset($_GET['txn']) || 
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

$transactionId = $_GET['txn'];
$user_id = (int) $_GET['user_id'];

// Fetch transaction
$stmt = $conn->prepare("
    SELECT us.*, 
        sp.name AS plan_name, 
        sp.price AS plan_price,
        sp.description AS plan_description,
        u.fullname,
        u.email
    FROM subscriptions us
    INNER JOIN subscription_plans sp ON us.plan_id = sp.id
    INNER JOIN users u ON us.user_id = u.id
    WHERE us.user_id = ? AND us.transaction_id = ?
");


$stmt->bind_param("is", $user_id, $transactionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Transaction not found.');
}

$data = $result->fetch_assoc();


// Create HTML
$html = "
    <!DOCTYPE html>
    <html>
        <head>
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    color: #333;
                }
                .invoice-box {
                    max-width: 800px;
                    margin: auto;
                    padding: 30px;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #2563eb;
                }
                .invoice-title {
                    font-size: 28px;
                    font-weight: bold;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .table th {
                    background: #2563eb;
                    color: white;
                    padding: 10px;
                    text-align: left;
                }
                .table td {
                    padding: 10px;
                    border-bottom: 1px solid #ddd;
                }
                .total {
                    text-align: right;
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 20px;
                }
                .footer {
                    margin-top: 40px;
                    font-size: 12px;
                    color: #777;
                    text-align: center;
                }
            </style>
        </head>
        <body>

            <div class='invoice-box'>
                <div class='header'>
                    <div class='logo'>DevHire</div>
                    <div class='invoice-title'>Subscription INVOICE</div>
                </div>

                <hr>

                <p>
                    <strong>Invoice #:</strong> {$transactionId}<br>
                    <strong>Date:</strong> {$data['created_at']}<br>
                    <strong>Billed To:</strong><br>
                    {$data['fullname']}<br>
                    {$data['email']}
                </p>

                <table class='table'>
                    <tr>
                        <th>Description</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                    </tr>
                    <tr>
                        <td>{$data['plan_description']}</td>
                        <td>{$data['plan_name']}</td>
                        <td>$ {$data['plan_price']}</td>
                    </tr>
                </table>

                <div class='total'>
                    Total: $ {$data['plan_price']}
                </div>

                <div class='footer'>
                    Thank you for your business.
                </div>
            </div>

        </body>
    </html>
";

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Force download
$dompdf->stream("Invoice-{$transactionId}.pdf", ["Attachment" => true]);
exit;