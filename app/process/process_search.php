<?php
// Database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

// Helper function for JSON response
function sendResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// Get query parameters
$q = trim($_GET['q'] ?? '');
$type = strtolower($_GET['type'] ?? 'people');

if (empty($q)) {
    sendResponse('error', 'Query too short', []);
}

$q = str_replace(['%', '_'], ['\%', '\_'], $q);

$like = '%' . $q . '%';
$limit = 50; // allow more results for main search page
$limit = (int) $limit;
$results = [];

try {
    if ($type === 'people') {
        // Search people by fullname or email from users table
        $stmt = $conn->prepare("SELECT u.id, u.usertoken, u.fullname, u.email, u.role, u.picture AS google_picture, d.profile_picture AS uploaded_picture
            FROM users u
            LEFT JOIN developers_profiles d ON u.id = d.user_id
            WHERE (u.fullname LIKE ? OR u.email LIKE ?)
            AND u.role NOT IN ('administrator','CEO')
            AND u.suspended_at IS NULL
            LIMIT $limit
        ");
        $stmt->bind_param("ss", $like, $like);
    } elseif ($type === 'career') {
        // Search by career (role in users table or primary_job_interest in developers_profiles)
        $stmt = $conn->prepare("SELECT DISTINCT u.id, u.usertoken, u.fullname, u.email, u.role, u.picture AS google_picture, d.profile_picture AS uploaded_picture
            FROM users u
            LEFT JOIN developers_profiles d ON u.id = d.user_id
            WHERE (u.role LIKE ? OR IFNULL(d.primary_job_interest,'') LIKE ?)
            AND u.role NOT IN ('administrator','CEO')
            AND u.suspended_at IS NULL
            LIMIT $limit
        ");
        $stmt->bind_param("ss", $like, $like);
    } else {
        sendResponse('error', 'Unknown search category', []);
    }

    $stmt->execute();
    $queryResult = $stmt->get_result();

    while ($row = $queryResult->fetch_assoc()) {
        // Use uploaded picture first, then Google picture, then default
        if (!empty($row['uploaded_picture'])) {
            $row['picture_url'] = "../" . $row['uploaded_picture'];
        } elseif (!empty($row['google_picture'])) {
            $row['picture_url'] = $row['google_picture']; // usually already a URL
        } else {
            $row['picture_url'] = "/assets/images/default-avatar.png";
        }
        $results[] = $row;
    }

    sendResponse('success', 'Search complete', $results);
} catch (Exception $e) {
    sendResponse('error', $e->getMessage(), []);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
