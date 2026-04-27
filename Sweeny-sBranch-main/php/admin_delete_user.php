<?php
// Start session to access logged-in user data
session_start();

// Set response format to JSON
header('Content-Type: application/json');

// Ensure the user is logged in AND is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'forbidden']);
    exit;
}

// Include database connection
include 'db_connect.php';

// Get userID from POST request and cast to integer
$userID = (int)($_POST['userID'] ?? 0);

// Validate userID
// - Must be greater than 0
// - Admin cannot delete their own account
if ($userID <= 0 || $userID === (int)$_SESSION['userID']) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'invalid_user']);
    exit;
}

// Prepare SQL statement to delete user
$stmt = $conn->prepare("DELETE FROM Users WHERE userID = ?");

// Bind userID parameter
$stmt->bind_param('i', $userID);

// Execute deletion
$stmt->execute();

if ($stmt->affected_rows === 0) {
    // No rows were deleted -> user did not exist
    http_response_code(404);
    echo json_encode(['error' => 'user_not_found']);
} else {
    // User was deleted
    echo json_encode(['ok' => true]);
}
?>