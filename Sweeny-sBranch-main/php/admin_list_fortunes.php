<?php
// Admin page uses this to show all fortunes.
// It reads from the original Fortune table only.
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

include 'db_connect.php';

$result = $conn->query("SELECT fortuneID, fortuneDescription FROM Fortune ORDER BY fortuneID DESC");
$fortunes = [];

while ($row = $result->fetch_assoc()) {
    $rawText = $row['fortuneDescription'];
    $isDeleted = substr($rawText, 0, 9) === '[DELETED]';
    $isEvil = substr($rawText, 0, 6) === '[EVIL]';
    $cleanText = trim(str_replace(['[EVIL]', '[DELETED]'], '', $rawText));

    $fortunes[] = [
        'fortuneID' => (int)$row['fortuneID'],
        'fortuneDescription' => $cleanText,
        'isEvil' => $isEvil ? 1 : 0,
        'isActive' => $isDeleted ? 0 : 1
    ];
}

echo json_encode($fortunes);
?>
