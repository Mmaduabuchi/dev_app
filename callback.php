<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();

// Google API client
require 'google_config.php';
// Database connection
require_once __DIR__ . '/config/databaseconnection.php';

function saveUser($google_id, $name, $email, $picture, $conn) {
    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE google_id = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $google_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // $stmt = $conn->prepare("UPDATE users SET fullname = ?, picture = ? WHERE id = ?");
        // $stmt->bind_param("ssi", $name, $picture, $user['id']);
        // $stmt->execute();

        $user['is_new'] = false; // mark as existing
    } else {
        // Generate unique usertoken
        do {
            $user_token = bin2hex(random_bytes(32));
            $check = $conn->prepare("SELECT id FROM users WHERE usertoken = ? LIMIT 1");
            $check->bind_param("s", $user_token);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();
        } while ($exists);

        $created_at = date('Y-m-d H:i:s');
        // default for new users
        $is_profile_complete = 0;
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (google_id, picture, fullname, email, password, usertoken, user_type, role, auth, is_profile_complete, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NULL, ?, 'talent', NULL, 'user', ?, ?, ?)");
        $stmt->bind_param("sssssiss", $google_id, $picture, $name, $email, $user_token, $is_profile_complete, $created_at, $created_at);
        $stmt->execute();

        $new_id = $stmt->insert_id;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $new_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $user['is_new'] = true; // mark as new
    }

    return $user;
}

// Handle OAuth redirect flow (when ?code=... comes back)
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token);
        $id_token = $token['id_token'] ?? null;

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $user = saveUser($payload['sub'], $payload['name'], $payload['email'], $payload['picture'], $conn);
            // Store user in session
            $_SESSION['user'] = $user;
            // Redirect based on whether the user is new or existing
            if ($user['is_new']) {
                header("Location: http://localhost/devhire/stack");
            } else {
                header("Location: http://localhost/devhire/dashboard");
            }
            exit;
        } else {
            exit("Invalid ID token!");
        }
    } else {
        exit("Error fetching access token!");
    }
}

// Handle One Tap flow (Google sends credential via POST)
if (isset($_POST['credential'])) {
    $id_token = $_POST['credential'];
    $payload = $client->verifyIdToken($id_token);

    if ($payload) {
        $user = saveUser($payload['sub'], $payload['name'], $payload['email'], $payload['picture'], $conn);
        // Store user in session
        $_SESSION['user'] = $user;
        // Redirect based on whether the user is new or existing
        if ($user['is_new']) {
            header("Location: http://localhost/devhire/stack");
        } else {
            header("Location: http://localhost/devhire/dashboard");
        }
        exit;
    } else {
        exit("Invalid ID Token!");
    }
}
// If neither code nor credential is present
echo "No auth code or credential received!";
