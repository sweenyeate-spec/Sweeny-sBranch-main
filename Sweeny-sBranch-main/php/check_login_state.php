<?php
// Start the session to access stored login data
session_start();

// Tell the browser that the response will be in JSON format
header('Content-Type: application/json');

// Return login status and admin status as a JSON object
echo json_encode([
    // true if userID exists in session (user is logged in)
    "loggedIn" => isset($_SESSION['userID']),

    // return admin status if set, otherwise default to 0 (not admin)
    "isAdmin" => $_SESSION['isAdmin'] ?? 0,
]);
?>