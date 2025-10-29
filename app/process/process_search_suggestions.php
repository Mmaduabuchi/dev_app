<?php
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

// Helper function for JSON response
function sendResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

header('Content-Type: application/json');

// Get query parameters
$q = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'People';

if (strlen($q) < 2) {
    sendResponse([]);
}

$like = '%' . $q . '%';
$limit = 20;
$results = [];

try {
    if ($type === 'People') {
        //Search people by fullname or email from users table
        $stmt = $conn->prepare(" SELECT id, fullname AS name, email FROM `users` WHERE fullname LIKE ? OR email LIKE ? LIMIT ?");
        $stmt->bind_param("ssi", $like, $like, $limit);
    } elseif ($type === 'Career') {
        //Search career-related info:
        //From users table (role)
        //From developers_profiles table (primary_job_interest)
        $stmt = $conn->prepare("SELECT u.id, u.fullname AS name, u.role AS title FROM `users` u WHERE u.role LIKE ?
            
            UNION
            
            SELECT d.user_id AS id, d.primary_job_interest AS name, NULL AS title FROM `developers_profiles` d WHERE d.primary_job_interest LIKE ? LIMIT ?
        ");
        $stmt->bind_param("ssi", $like, $like, $limit);
    } else {
        sendResponse([]); // Unknown type
    }

    $stmt->execute();
    $queryResult = $stmt->get_result();

    while ($row = $queryResult->fetch_assoc()) {
        $results[] = $row;
    }

    sendResponse(array_values($results));
} catch (Exception $e) {
    sendResponse(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}