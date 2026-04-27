<?php
// Adds a new fortune from the admin dashboard.
// Evil fortunes are saved with [EVIL] at the front so the database does not need a new column.
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

include 'db_connect.php';

$text = trim($_POST['fortuneDescription'] ?? '');
$isEvil = ($_POST['isEvil'] ?? '0') === '1';

if ($text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'empty_fortune']);
    exit;
}

// Do not double-prefix if the admin already typed it.
$text = preg_replace('/^\[EVIL\]\s*/i', '', $text);
$storedText = $isEvil ? '[EVIL] ' . $text : $text;

$stmt = $conn->prepare("INSERT INTO Fortune (fortuneDescription) VALUES (?)");
$stmt->bind_param('s', $storedText);
$stmt->execute();

echo json_encode(['ok' => true, 'fortuneID' => $stmt->insert_id]);
?>
