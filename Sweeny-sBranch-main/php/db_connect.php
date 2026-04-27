<?php
// Database server settings
$servername = "localhost";    // Host
$username = "root";           // MySQL username
$password = "";               // MySQL password
$dbname = "oracle_of_shrimp"; // Database name

// Enable MySQLi error reporting (throws exceptions instead of silent failures)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Set character encoding to support full UTF-8 (including emojis, special characters)
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // If connection fails, return HTTP 500 (server error)
    http_response_code(500);

    // Stop script and show error message (useful for debugging)
    die("Connection failed: " . $e->getMessage());
}
?>
