<?php
// Deletes a fortune if possible.
// If it already appears in FortuneLog, we mark it [DELETED] instead so old logs do not break.
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

include 'db_connect.php';

$fortuneID = (int)($_POST['fortuneID'] ?? 0);

if ($fortuneID <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_input']);
    exit;
}

$check = $conn->prepare("SELECT COUNT(*) AS total FROM FortuneLog WHERE fortuneID = ?");
$check->bind_param('i', $fortuneID);
$check->execute();
$row = $check->get_result()->fetch_assoc();

if ((int)$row['total'] > 0) {
    // It has been used before, so hide it instead of removing it.
    $get = $conn->prepare("SELECT fortuneDescription FROM Fortune WHERE fortuneID = ?");
    $get->bind_param('i', $fortuneID);
    $get->execute();
    $fortune = $get->get_result()->fetch_assoc();
    $text = $fortune ? $fortune['fortuneDescription'] : 'Removed fortune';

    if (!substr($text, 0, 9) === '[DELETED]') {
        $text = '[DELETED] ' . $text;
    }

    $stmt = $conn->prepare("UPDATE Fortune SET fortuneDescription = ? WHERE fortuneID = ?");
    $stmt->bind_param('si', $text, $fortuneID);
    $stmt->execute();
    echo json_encode(['ok' => true, 'inactiveOnly' => true]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM Fortune WHERE fortuneID = ?");
$stmt->bind_param('i', $fortuneID);
$stmt->execute();

echo json_encode(['ok' => true, 'inactiveOnly' => false]);
?>
