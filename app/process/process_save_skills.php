<?php
//start session
session_start();
//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

//helper function for response
function sendResponse($status, $message)
{
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
} else {
    //get usertoken and skills form inputs
    $usertoken = $_POST['token'] ?? '';
    $skills = $_POST['skills'] ?? '';

    //validate users inputs
    if (empty($usertoken) || empty($skills)) {
        sendResponse("error", "All fields are required.");
    }

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        sendResponse('error', 'User not logged in.');
    }

    // Get user ID from session
    $userId = $_SESSION['user']['id'];

    //decode skills json
    $skills = json_decode($skills, true);
    if (!is_array($skills) || empty($skills)) {
        sendResponse("error", "Invalid skills data.");
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Fetch the user token from the database
        $stmt = $conn->prepare("SELECT usertoken, suspended_at FROM `users` WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            sendResponse('error', 'User not found.');
        }
        $row = $result->fetch_assoc();
        $currentUserToken = $row['usertoken'];
        $suspended_at = $row['suspended_at'];
        $stmt->close();

        // Verify the user token
        if ($usertoken !== $currentUserToken) {
            sendResponse('error', 'Invalid user token.');
        }

        //Check if user is still active
        if (!is_null($suspended_at)) {
            sendResponse('error', 'Account is suspended. Action not allowed.');
        }

        foreach ($skills as $skill_name) {
            // Trim and validate skill name
            $skill_name = ucfirst(strtolower(trim($skill_name)));
            if (empty($skill_name) || !preg_match("/^[a-zA-Z0-9\s\.\,\-\&\+\/]+$/", $skill_name)) {
                throw new Exception("Invalid skill name: " . htmlspecialchars($skill_name));
            }

            // Insert skill into skills table if it doesn't exist
            $stmt = $conn->prepare("INSERT IGNORE INTO `skills` (skill_name) VALUES (?)");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("s", $skill_name);
            $stmt->execute();
            $stmt->close();

            // Get the skill ID
            $stmt = $conn->prepare("SELECT id FROM `skills` WHERE skill_name = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("s", $skill_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Failed to retrieve skill ID for: " . htmlspecialchars($skill_name));
            }
            $row = $result->fetch_assoc();
            $skill_id = $row['id'];
            $stmt->close();

            //check how many added skills the user has
            $stmt = $conn->prepare("SELECT COUNT(*) AS skill_count FROM `user_skills` WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $skill_count = $row['skill_count'];
            $stmt->close();

            if ($skill_count >= 20) {
                throw new Exception('You can only add up to twenty skills.');
            }

            // Associate skill with user in user_skills table
            $stmt = $conn->prepare("INSERT IGNORE INTO `user_skills` (user_id, skill_id, created_at) VALUES (?, ?, NOW())");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("ii", $userId, $skill_id);
            $stmt->execute();
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();
        sendResponse('success', 'Skills saved successfully.');
    } catch (Exception $e) {
        $conn->rollback(); // Rollback if error occurs
        sendResponse('error', 'An error occurred: ' . $e->getMessage());
    } finally {
        // Close the database connection
        $conn->close();
    }
}
