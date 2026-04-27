<?php
// Start session to access logged-in user info
session_start();

// Set response type to JSON
header('Content-Type: application/json');

// Check if user is logged in AND is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'forbidden']);
    exit;
}

// Include database connection
include 'db_connect.php';

// Get userID and action from POST request
$userID = (int)($_POST['userID'] ?? 0);
$action = $_POST['action'] ?? '';

// Validate userID
if ($userID <= 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'invalid_user']);
    exit;
}

// ACTION: TOGGLE ADMIN STATUS
if ($action === 'toggle_admin') {

    // Prevent admin from changing their own admin status
    if ($userID === (int)$_SESSION['userID']) {
        http_response_code(400);
        echo json_encode(['error' => 'cannot_change_self']);
        exit;
    }

    // Toggle isAdmin (1 -> 0 or 0 -> 1)
    $stmt = $conn->prepare("UPDATE Users SET isAdmin = IF(isAdmin = 1, 0, 1) WHERE userID = ?");
    $stmt->bind_param('i', $userID);
    $stmt->execute();

    // Return success response
    echo json_encode(['ok' => true]);
    exit;
}

// ACTION: RESET PASSWORD
if ($action === 'reset_password') {

    // Get new password from request
    $newPassword = $_POST['newPassword'] ?? '';

    // Validate password strength (minimum length)
    if (strlen($newPassword) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'weak_password']);
        exit;
    }

    // Hash the new password securely
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE Users SET passwrd = ? WHERE userID = ?");
    $stmt->bind_param('si', $hash, $userID);
    $stmt->execute();

    // Return success response
    echo json_encode(['ok' => true]);
    exit;
}

// If action is not recognized
http_response_code(400);
echo json_encode(['error' => 'invalid_action']);
?>