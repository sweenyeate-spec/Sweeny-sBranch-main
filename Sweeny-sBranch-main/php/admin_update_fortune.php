<?php
// Edits an existing fortune from the admin dashboard.
// It keeps the database the same by storing evil/deleted status as a text prefix.
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

include 'db_connect.php';

$fortuneID = (int)($_POST['fortuneID'] ?? 0);
$text = trim($_POST['fortuneDescription'] ?? '');
$isEvil = ($_POST['isEvil'] ?? '0') === '1';
$isActive = ($_POST['isActive'] ?? '1') === '1';

if ($fortuneID <= 0 || $text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_input']);
    exit;
}

$text = preg_replace('/^\[EVIL\]\s*/i', '', $text);
$text = preg_replace('/^\[DELETED\]\s*/i', '', $text);

$storedText = $isEvil ? '[EVIL] ' . $text : $text;
if (!$isActive) {
    $storedText = '[DELETED] ' . $storedText;
}

$stmt = $conn->prepare("UPDATE Fortune SET fortuneDescription = ? WHERE fortuneID = ?");
$stmt->bind_param('si', $storedText, $fortuneID);
$stmt->execute();

echo json_encode(['ok' => true]);
?>
