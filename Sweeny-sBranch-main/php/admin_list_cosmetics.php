<?php
// Start session to access logged-in user data
session_start();

// Tell browser this file returns JSON
header('Content-Type: application/json');

// Check if user is logged in AND is an admin
if (!isset($_SESSION['userID']) || (int)($_SESSION['isAdmin'] ?? 0) !== 1) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'forbidden']); // Send error message
    exit; // Stop script
}

// Include database connection
include 'db_connect.php';

// Get the type from the URL (skin or item), clean it and make lowercase
$type = strtolower(trim($_GET['type'] ?? ''));

// Validate that type is either 'skin' or 'item'
if ($type !== 'skin' && $type !== 'item') {
    http_response_code(400); // Bad request
    echo json_encode(['error' => 'invalid_type']);
    exit;
}

// Prepare SQL query to get cosmetics of a specific type
$stmt = $conn->prepare("
    SELECT cosmeticID, cosmeticDescription, cosmeticType, rarityWeight, acquisitionDate
    FROM Cosmetic
    WHERE LOWER(cosmeticType) = ?
    ORDER BY cosmeticID DESC
");

// Bind the type parameter safely to prevent SQL injection
$stmt->bind_param('s', $type);

// Execute the query
$stmt->execute();

// Get the results from the query
$result = $stmt->get_result();

// Create an empty array to store results
$cosmetics = [];

// Loop through each row returned from the database
while ($row = $result->fetch_assoc()) {
    $cosmetics[] = $row; // Add each cosmetic to the array
}

// Return cosmetics as JSON to the frontend
echo json_encode($cosmetics);
?>