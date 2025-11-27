<?php
//start session
session_start();

// If admin is logged in → redirect immediately
if(!empty($_SESSION['admin']) && $_SESSION['admin_logged_in'] === true){
    header("Location: /devhire/admin/dashboard/home");
    exit();
}

// If a normal user session exists, logout user only
if(!empty($_SESSION['user'])){
    session_destroy();
}