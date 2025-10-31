<?php
//start session
session_start();

//authenticate user
if (!isset($_SESSION['onboarding_id'])) {
    header("location: /devhire/");
    exit;
}

//get user's onboarding id token
$onboarding_id = $_SESSION['onboarding_id'];