<?php
// // Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
// Database connection
require_once __DIR__ . '/../config/databaseconnection.php';

//response function
function response($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'step1') {
        $legalname = trim($_POST['legalname'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $citizenship = trim($_POST['citizenship'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $english_proficiency = trim($_POST['english_proficiency'] ?? '');
        $education_level = trim($_POST['education_level'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        // Basic validation
        if (empty($legalname) || empty($location) || empty($citizenship) || empty($phone) || empty($english_proficiency) || empty($education_level) || empty($bio)) {
            response('error', 'All fields are required.');
        }
        //validate phone number
        if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
            response('error', 'Invalid phone number format.');
        }
        //validate name (only letters and spaces)
        if (!preg_match('/^[a-zA-Z\s]+$/', $legalname)) {
            response('error', 'Name can only contain letters and spaces.');
        }

        //validate lenglish_proficiency
        $valid_proficiencies = ['basic', 'conversational', 'fluent', 'native'];
        if (!in_array($english_proficiency, $valid_proficiencies)) {
            response('error', 'Invalid English proficiency level.');
        }

        //validate education level
        $valid_education_levels = ['high_school', 'diploma', 'phd', 'vocational', 'associate', 'bachelor', 'master', 'self_taught', 'other', 'bootcamp'];
        if (!in_array($education_level, $valid_education_levels)) {
            response('error', 'Invalid education level.');
        }

        //check if user is logged in
        $user_id = $_SESSION['user']['id'] ?? null;
        if (!$user_id) {
            response('error', 'User not logged in.');
        }

        // Save to session
        $_SESSION['screening_wizard'] = [
            'legalname' => $legalname,
            'location' => $location,
            'citizenship' => $citizenship,
            'phone' => $phone,
            'english_proficiency' => $english_proficiency,
            'education_level' => $education_level,
            'bio' => $bio
        ];

        try {
            $created_at = date('Y-m-d H:i:s');

            // Check if profile already exists
            $checkStmt = $conn->prepare("SELECT id FROM developers_profiles WHERE user_id = ?");
            if (!$checkStmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $checkStmt->bind_param("i", $user_id);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                // Update existing profile
                $stmt = $conn->prepare("UPDATE developers_profiles SET action=?, legal_name=?, location=?, citizenship=?, phone_number=?, english_proficiency=?, education_level=?, bio=?, updated_at=? WHERE user_id=?");
                if (!$stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $stmt->bind_param("sssssssssi", $action, $legalname, $location, $citizenship, $phone, $english_proficiency, $education_level, $bio, $created_at, $user_id);
            } else {
                // Insert new profile
                $stmt = $conn->prepare("INSERT INTO developers_profiles (user_id, action, legal_name, location, citizenship, phone_number, english_proficiency, education_level, bio, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $stmt->bind_param("isssssssss", $user_id, $action, $legalname, $location, $citizenship, $phone, $english_proficiency, $education_level, $bio, $created_at);
            }

            if ($stmt->execute()) {
                $stmt->close();
                response('success', 'Step 1 data saved.');
            } else {
                throw new Exception('Database error: ' . $stmt->error);
            }
        } catch (Exception $e) {
            response('error', $e->getMessage());
        }
    } elseif ($action === 'step2') {
        $experience = trim($_POST['experience'] ?? '');
        $job_interest = trim($_POST['job_interest'] ?? '');
        $industry = trim($_POST['industry'] ?? '');
        $certification = trim($_POST['certification'] ?? '');
        $job_commitment = trim($_POST['job_commitment'] ?? '');
        $rate = trim($_POST['rate'] ?? '');
        $website = trim($_POST['website'] ?? null);
        $linkedln = trim($_POST['linkedln'] ?? null);
        $github = trim($_POST['github'] ?? null);

        // Basic validation
        if (empty($experience) || empty($job_interest) || empty($industry) || empty($job_commitment) || empty($rate)) {
            response('error', 'All fields are required except website, linkedln and github.');
        }
        //validate rate (must be a number)
        if (!is_numeric($rate) || $rate < 0) {
            response('error', 'Rate must be a positive number.');
        }
        //validate job commitment
        $valid_commitments = ['full_time', 'part_time', 'hourly', 'internship', 'contract'];
        if (!in_array($job_commitment, $valid_commitments)) {
            response('error', 'Invalid job commitment.');
        }
        //validate experience
        $valid_experiences = ['1', '1-5', '5-8', '8+'];
        if (!in_array($experience, $valid_experiences)) {
            response('error', 'Invalid experience level.');
        }
        //check if user is logged in
        $user_id = $_SESSION['user']['id'] ?? null;
        if (!$user_id) {
            response('error', 'User not logged in.');
        }
        // Save to session
        $_SESSION['screening_wizard'] = array_merge($_SESSION['screening_wizard'] ?? [], [
            'experience' => $experience,
            'job_interest' => $job_interest,
            'industry' => $industry,
            'certification' => $certification,
            'job_commitment' => $job_commitment,
            'rate' => $rate,
            'website' => $website,
            'linkedln' => $linkedln,
            'github' => $github
        ]);

        try {
            $created_at = date('Y-m-d H:i:s');

            // Check if profile already exists
            $checkStmt = $conn->prepare("SELECT id FROM developers_profiles WHERE user_id = ?");
            if (!$checkStmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $checkStmt->bind_param("i", $user_id);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                // Update existing profile
                $stmt = $conn->prepare("UPDATE developers_profiles SET action=?, years_of_experience=?, primary_job_interest=?, industry_experience=?, certificate=?, job_commitment=?, preferred_hourly_rate=?, website=?, linkedin=?, github=?, updated_at=? WHERE user_id=?");
                if (!$stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $stmt->bind_param("sssssssssssi", $action, $experience, $job_interest, $industry, $certification, $job_commitment, $rate, $website, $linkedln, $github, $created_at, $user_id);
            } else {
                response('error', 'Profile not found. Please complete Step 1 first.');
            }

            if ($stmt->execute()) {
                $stmt->close();
                response('success', 'Step 2 data saved.');
            } else {
                throw new Exception('Database error: ' . $stmt->error);
            }
        } catch (Exception $e) {
            response('error', $e->getMessage());
        }
    } elseif ($action === 'completed') {
        // Handle file uploads
        $profilePhoto = $_FILES['profilePhoto'] ?? null;
        $resume = $_FILES['resume'] ?? null;

        if (!$profilePhoto || $profilePhoto['error'] !== UPLOAD_ERR_OK) {
            response('error', 'Profile photo upload failed.');
        }
        if (!$resume || $resume['error'] !== UPLOAD_ERR_OK) {
            response('error', 'Resume upload failed.');
        }

        // Validate file types and sizes
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedResumeTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($profilePhoto['type'], $allowedImageTypes) || $profilePhoto['size'] > $maxFileSize) {
            response('error', 'Invalid profile photo. Only JPG, PNG, WEBP, GIF files under 5MB are allowed.');
        }
        if (!in_array($resume['type'], $allowedResumeTypes) || $resume['size'] > $maxFileSize) {
            response('error', 'Invalid resume. Only PDF, DOC, DOCX files under 5MB are allowed.');
        }

        //check if user is logged in
        $user_id = $_SESSION['user']['id'] ?? null;
        if (!$user_id) {
            response('error', 'User not logged in.');
        }

        // Move uploaded files to permanent location
        $uploadDir = __DIR__ . '/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Extract safe extensions
        $profileExt = strtolower(pathinfo($profilePhoto['name'], PATHINFO_EXTENSION));
        $resumeExt  = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

        // Generate unique safe filenames
        $profilePhotoPath = $uploadDir . uniqid('profile_', true) . '.' . $profileExt;
        $resumePath = $uploadDir . uniqid('resume_', true) . '.' . $resumeExt;

        // Move files
        if (!move_uploaded_file($profilePhoto['tmp_name'], $profilePhotoPath)) {
            response('error', 'Failed to save profile photo.');
        }
        if (!move_uploaded_file($resume['tmp_name'], $resumePath)) {
            response('error', 'Failed to save resume.');
        }

        // Begin transaction
        $conn->begin_transaction();
        try {
            $created_at = date('Y-m-d H:i:s');
            // Update profile with file paths and set action to 'completed'
            $stmt = $conn->prepare("UPDATE developers_profiles SET action=?, profile_picture=?, resume=?, updated_at=? WHERE user_id=?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }

            $completedAction = 'completed';
            $stmt->bind_param("ssssi", $completedAction, $profilePhotoPath, $resumePath, $created_at, $user_id);
            if (!$stmt->execute()) {
                throw new Exception('Database error: ' . $stmt->error);
            }
            $stmt->close();

            //add 1 to is_profile_complete in users table
            $stmt = $conn->prepare("UPDATE users SET is_profile_complete = is_profile_complete + 1 WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                throw new Exception('Database error: ' . $stmt->error);
            }
            $stmt->close();

            // Commit transaction
            $conn->commit();

            // Clear session data
            unset($_SESSION['screening_wizard']);
            response('success', 'Profile completed successfully.');
        } catch (Exception $e) {
            $conn->rollback();
            response('error', $e->getMessage());
        }
        
    } else {
        response('error', 'Invalid action.');
    }
} else {
    response('error', 'Invalid request method.');
}
