<?php
// Start the session to access logged-in user data
session_start();

// Tell the browser that this response will be JSON
header('Content-Type: application/json');

// Check if the user is logged in (userID stored in session)
if (!isset($_SESSION['userID'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

// Include database connection
include 'db_connect.php';

// Prepare SQL query to get user information by userID
$sql = "SELECT userID, username, email, isAdmin, createdAt FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sql);

// Bind the session userID as an integer parameter
$stmt->bind_param('i', $_SESSION['userID']);

// Execute the query
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Fetch user data as an associative array
$user = $result->fetch_assoc();

// If no user is found in the database
if (!$user) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'user_not_found']);
    exit;
}

// Return the user data as JSON
echo json_encode($user);
?>