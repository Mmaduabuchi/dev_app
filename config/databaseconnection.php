<?php
$servername = "localhost";
$username = "root";
$password = "0987654321";

// Create connection
$conn = mysqli_connect($servername, $username, $password, 'devhire');

// Check connection
if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}