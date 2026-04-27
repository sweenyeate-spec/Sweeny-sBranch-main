<?php
// Delete old rarity map
// Start session to access logged-in user info
session_start();

// Tell browser this script returns JSON
header('Content-Type: application/json');

// Check if user is logged in AND is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'forbidden']); // Send error message
    exit; // Stop execution
}

// Include database connection
include 'db_connect.php';

// Get and clean input values from POST request
$type = strtolower(trim($_POST['type'] ?? ''));   // 'skin' or 'item'
$name = trim($_POST['name'] ?? '');               // cosmetic name
$rarity = strtolower(trim($_POST['rarity'] ?? '')); // rarity string

// Validate required inputs
if (($type !== 'skin' && $type !== 'item') || $name === '') {
    http_response_code(400); // Bad request
    echo json_encode(['error' => 'invalid_input']);
    exit;
}

// **DELETE--- old rarity map**
// Map rarity text to numeric value used in database
$rarityMap = [
    'common' => 1,
    'uncommon' => 2,
    'rare' => 3,
    'legendary' => 4
];

// Validate rarity exists in map
if (!isset($rarityMap[$rarity])) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_rarity']);
    exit;
}

// Convert rarity string to number
$rarityWeight = $rarityMap[$rarity];

// Check if cosmetic already exists (same name and type)
$check = $conn->prepare("
    SELECT cosmeticID
    FROM Cosmetic
    WHERE LOWER(cosmeticType) = ? AND LOWER(cosmeticDescription) = LOWER(?)
    LIMIT 1
");

// Bind parameters safely
$check->bind_param('ss', $type, $name);

// Execute check query
$check->execute();

// Get existing result (if any)
$existing = $check->get_result()->fetch_assoc();

// If already exists, stop and return error
if ($existing) {
    http_response_code(400);
    echo json_encode(['error' => 'already_exists']);
    exit;
}

// Prepare insert query to add new cosmetic
$stmt = $conn->prepare("
    INSERT INTO Cosmetic (rarityWeight, cosmeticDescription, cosmeticType)
    VALUES (?, ?, ?)
");

// Bind values to insert query
$stmt->bind_param('iss', $rarityWeight, $name, $type);

// Execute insert
$stmt->execute();

// Return success and new cosmetic ID
echo json_encode([
    'ok' => true,
    'cosmeticID' => $stmt->insert_id
]);
?>