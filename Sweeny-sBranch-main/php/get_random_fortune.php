<?php
// This file picks a random fortune and saves it to the user's history.
// It uses the original database tables only. No schema changes are needed.
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

include 'db_connect.php';

$userID = (int)$_SESSION['userID'];
$evilMode = ($_POST['evil'] ?? '0') === '1' ? 1 : 0;

// Make sure the user has one shrimp birthday saved.
// The existing database uses a DATE column, so we save today's date.
$shrimpCheck = $conn->prepare("SELECT shrimpID FROM Shrimp WHERE ownerUserID = ? LIMIT 1");
$shrimpCheck->bind_param('i', $userID);
$shrimpCheck->execute();
$shrimp = $shrimpCheck->get_result()->fetch_assoc();

if (!$shrimp) {
    $shrimpName = 'Oracle Shrimp';
    $today = date('Y-m-d');
    $makeShrimp = $conn->prepare("INSERT INTO Shrimp (ownerUserID, shrimpName, shrimpBirthday) VALUES (?, ?, ?)");
    $makeShrimp->bind_param('iss', $userID, $shrimpName, $today);
    $makeShrimp->execute();
}

// Evil fortunes are marked in the text with [EVIL].
// This keeps the evil toggle working without adding an isEvil column.
if ($evilMode === 1) {
    $sql = "SELECT fortuneID, fortuneDescription FROM Fortune WHERE fortuneDescription LIKE '[EVIL]%' AND fortuneDescription NOT LIKE '[DELETED]%' ORDER BY RAND() LIMIT 1";
} else {
    $sql = "SELECT fortuneID, fortuneDescription FROM Fortune WHERE fortuneDescription NOT LIKE '[EVIL]%' AND fortuneDescription NOT LIKE '[DELETED]%' ORDER BY RAND() LIMIT 1";
}

$result = $conn->query($sql);
$fortune = $result->fetch_assoc();

// If there are no evil fortunes yet, fall back to a regular fortune so the button still works.
if (!$fortune && $evilMode === 1) {
    $result = $conn->query("SELECT fortuneID, fortuneDescription FROM Fortune WHERE fortuneDescription NOT LIKE '[EVIL]%' AND fortuneDescription NOT LIKE '[DELETED]%' ORDER BY RAND() LIMIT 1");
    $fortune = $result->fetch_assoc();
}

if (!$fortune) {
    http_response_code(404);
    echo json_encode(['error' => 'no_fortunes']);
    exit;
}

$isEvil = substr($fortune['fortuneDescription'], 0, 6) === '[EVIL]' ? 1 : 0;
$cleanText = trim(str_replace('[EVIL]', '', $fortune['fortuneDescription']));

// Count first so we can save the next fortuneCount value in the existing FortuneLog table.
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM FortuneLog WHERE userID = ?");
$countStmt->bind_param('i', $userID);
$countStmt->execute();
$countRow = $countStmt->get_result()->fetch_assoc();
$nextCount = ((int)$countRow['total']) + 1;

// Save the fortune to the existing database log.
// The original table does not have a timestamp column, so timestamps are saved below in a small JSON file.
$log = $conn->prepare("INSERT INTO FortuneLog (userID, fortuneID, fortuneCount) VALUES (?, ?, ?)");
$log->bind_param('iii', $userID, $fortune['fortuneID'], $nextCount);
$log->execute();
$logID = $log->insert_id;

$receivedAt = date('Y-m-d H:i:s');

// Save timestamp details outside the database because the database cannot be changed.
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}
$historyFile = $dataDir . '/fortune_history.json';
$history = [];
if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true);
    if (!is_array($history)) {
        $history = [];
    }
}
$history[] = [
    'fortuneLogID' => $logID,
    'userID' => $userID,
    'fortuneID' => (int)$fortune['fortuneID'],
    'fortuneDescription' => $cleanText,
    'isEvil' => $isEvil,
    'receivedAt' => $receivedAt
];
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));

// Send data back to JavaScript.
echo json_encode([
    'ok' => true,
    'fortuneID' => (int)$fortune['fortuneID'],
    'fortuneDescription' => $cleanText,
    'isEvil' => $isEvil,
    'fortuneCount' => $nextCount,
    'receivedAt' => $receivedAt
]);
?>
