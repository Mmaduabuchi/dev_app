<?php
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

// Helper function for JSON response
function sendResponse($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Get query parameter
$q = $_GET['q'] ?? '';

if (strlen($q) < 1) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Prepare and execute query safely
$stmt = $conn->prepare("SELECT skill_name FROM skills WHERE skill_name LIKE ? LIMIT 10");
if (!$stmt) {
    sendResponse('error', 'Database error: ' . $conn->error);
}

$like_q = '%' . $q . '%';
$stmt->bind_param("s", $like_q);
$stmt->execute();
$result = $stmt->get_result();

$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = ['name' => $row['skill_name']];
}

$stmt->close();
$conn->close();

// Output JSON
header('Content-Type: application/json');
echo json_encode($skills);
