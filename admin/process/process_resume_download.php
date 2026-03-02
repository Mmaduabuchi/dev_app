<?php
if (!isset($_GET['file'])) {
    die('File not specified.');
}

$file = $_GET['file'];

// Security: prevent directory traversal
$file = str_replace('..', '', $file);

// Full path to file
$path = __DIR__ . '/../../' . $file;

if (!file_exists($path)) {
    die('File not found.');
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'. basename($path) .'"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;