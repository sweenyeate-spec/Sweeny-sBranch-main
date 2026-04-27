<?php
session_start();
header('Content-Type: application/json');

// Check if logged in user is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

include 'db_connect.php';

// Get inputs
$type = strtolower(trim($_POST['type'] ?? ''));
$cosmeticID = (int)($_POST['cosmeticID'] ?? 0);

// Validate inputs
if (($type !== 'skin' && $type !== 'item') || $cosmeticID <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_input']);
    exit;
}

// Delete the cosmetic only if both ID and type match
$stmt = $conn->prepare("
    DELETE FROM Cosmetic
    WHERE cosmeticID = ? AND LOWER(cosmeticType) = ?
");

$stmt->bind_param('is', $cosmeticID, $type);
$stmt->execute();

// If nothing was deleted, return error
if ($stmt->affected_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

// Success
echo json_encode(['ok' => true]);
?>