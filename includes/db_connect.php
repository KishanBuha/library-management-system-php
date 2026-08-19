<?php
// Database connection file

// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "library_management";

// Enable error reporting for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create connection
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4"); // Set character set to avoid encoding issues
} catch (mysqli_sql_exception $e) {
    // Log the error and display a generic message
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}
?>