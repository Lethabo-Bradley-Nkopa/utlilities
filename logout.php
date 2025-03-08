<?php
session_start(); // Ensure session is started before unsetting

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Prevent browser caching to avoid back button login issue
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// Redirect to login page
header("Location: login.php");
exit();
?>