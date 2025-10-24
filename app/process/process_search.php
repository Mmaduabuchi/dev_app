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

