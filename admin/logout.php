<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit(); // Ensure no further code is executed
?>
