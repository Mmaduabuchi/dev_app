<?php
// Include your database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

if (!isset($_POST['user_id'])) {
    die("User ID not provided.");
}

$user_id = intval($_POST['user_id']);

// fetch user data from database
$stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// fetch user profile from developers_profiles table
$stmt = $conn->prepare("SELECT * FROM `developers_profiles` WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$user || !$profile) {
    die("User not found.");
}

// Prepare data
$data = [
    'Full Name' => $user['fullname'],
    'Email' => $user['email'],
    'Phone Number' => $profile['phone_number'],
    'Legal Name' => $profile['legal_name'],
    'Bio' => $profile['bio'],
    'Website' => $profile['website'],
    'LinkedIn' => $profile['linkedin'],
    'Github' => $profile['github'],
    'Citizenship' => $profile['citizenship'],
    'English Proficiency' => $profile['english_proficiency'],
    'Education Level' => $profile['education_level'],
    'Years of Experience' => $profile['years_of_experience'],
    'Primary Job Interest' => $profile['primary_job_interest'],
    'Industry Experience' => $profile['industry_experience'],
    'Certifications' => $profile['certifications'],
    'Job Commitment' => $profile['job_commitment'],
    'Preferred Hourly Rate' => $profile['preferred_hourly_rate'] . ' USD',
    'Created At' => $user['created_at']
];

// Send headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=user_' . $user_id . '_profile.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write header row
fputcsv($output, array_keys($data));

// Write user data row
fputcsv($output, array_values($data));

fclose($output);
exit;