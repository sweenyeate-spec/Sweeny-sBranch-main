<?php
// Start session so we can store login data
session_start();

// Include database connection
include 'db_connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('invalid');
}

// Get username and password from form input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Stop if either field is empty
if ($username === '' || $password === '') {
    exit('invalid');
}

// Look up the user by username
$sql = "SELECT userID, username, email, passwrd, isAdmin FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit('invalid');
}

// Bind username safely
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if exactly one user was found
if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify entered password against hashed password in database
    if (password_verify($password, $user['passwrd'])) {
        // Save user info in session
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['isAdmin'] = (int)$user['isAdmin'];

        // Return different responses for admin and regular user
        if ((int)$user['isAdmin'] === 1) {
            echo 'admin';
        } else {
            echo 'ok';
        }
    } else {
        echo 'invalid';
    }
} else {
    echo 'invalid';
}

// Clean up
$stmt->close();
$conn->close();
?>