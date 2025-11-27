<?php
//start session
session_start();
//database connection
require_once __DIR__ . '/../../config/databaseconnection.php';

// --- Hash password (vanilla PHP) ---
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// --- Default users to insert ---
$users = [
    [
        'google_id' => null,
        'picture' => null,
        'fullname' => 'Super Admin',
        'email' => 'devhireadmin@devhire.com',
        'password' => hashPassword('1234567890'),
        'usertoken' => bin2hex(random_bytes(30)),
        'tel' => null,
        'user_type' => 'admin',
        'role' => 'super-admin',
        'auth' => 'admin',
        'is_profile_complete' => 1,
        'suspended_at' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'deleted_at' => null
    ],

    // [
    //     'google_id'           => 'GOOGLE12345',
    //     'picture'             => 'https://example.com/user1.png',
    //     'fullname'            => 'Test User',
    //     'email'               => 'user@example.com',
    //     'password'            => hashPassword('userpassword'),
    //     'usertoken'           => bin2hex(random_bytes(30)),
    //     'tel'                 => '08098765432',
    //     'user_type'           => 'customer',
    //     'role'                => 'user',
    //     'auth'                => 1,
    //     'is_profile_complete' => 0,
    //     'suspended_at'        => null,
    //     'created_at'          => date('Y-m-d H:i:s'),
    //     'updated_at'          => date('Y-m-d H:i:s'),
    //     'deleted_at'          => null
    // ]
];

// --- Insert data ---
foreach ($users as $user) {

    $stmt = $conn->prepare(" INSERT INTO users (
            google_id, picture, fullname, email, password, usertoken, tel,
            user_type, role, auth, is_profile_complete, suspended_at, created_at, updated_at, deleted_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssssiiissss",
        $user['google_id'],
        $user['picture'],
        $user['fullname'],
        $user['email'],
        $user['password'],
        $user['usertoken'],
        $user['tel'],
        $user['user_type'],
        $user['role'],
        $user['auth'],
        $user['is_profile_complete'],
        $user['suspended_at'],
        $user['created_at'],
        $user['updated_at'],
        $user['deleted_at']
    );

    if ($stmt->execute()) {
        echo "User inserted: " . $user['email'] . "<br>";
    } else {
        echo "Error inserting " . $user['email'] . ": " . $stmt->error . "<br>";
    }

    $stmt->close();
}

$conn->close();

echo "<br>Admin seeding completed!";
