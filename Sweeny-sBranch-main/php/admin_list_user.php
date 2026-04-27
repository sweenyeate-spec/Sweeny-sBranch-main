<?php
// Start session so we can access logged-in user info (userID, isAdmin)
session_start();

// Tell browser this file returns JSON
header('Content-Type: application/json');

// Check if user is logged in AND is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403); // Forbidden response
    echo json_encode(['error' => 'forbidden']); // Send error message
    exit; // Stop script
}

// Include database connection
include 'db_connect.php';

// Query the database for all users (newest first)
$result = $conn->query("SELECT userID, username, email, isAdmin FROM Users ORDER BY userID DESC");

// Create an empty array to store users
$users = [];

// Loop through each row returned from the database
while ($row = $result->fetch_assoc()) {
    $users[] = $row; // Add each user to the array
}

// Return users as JSON to the frontend
echo json_encode($users);
?>