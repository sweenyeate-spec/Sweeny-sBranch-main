<?php
// Start the session so we can store user data after signup
session_start();

// Include database connection file (creates $conn)
include 'db_connect.php';

// Only allow POST requests (form submissions)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('invalid');
}

// Get and clean input values from the form
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Check if any required field is empty
if ($username === '' || $email === '' || $password === '') {
    exit('missing');
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('invalid_email');
}

// Enforce minimum password length
if (strlen($password) < 6) {
    exit('weak_password');
}

// Check if username or email already exists in database
$sql = "SELECT userID FROM Users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

// If a match is found, user already exists
if ($result && $result->num_rows > 0) {
    exit('exists');
}

// Hash the password securely before storing it
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into database
$sql = "INSERT INTO Users (username, email, passwrd) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashPassword);

// Execute insert query
if ($stmt->execute()) {
    // Store user info in session (auto-login after signup)
    $_SESSION['userID'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    $_SESSION['isAdmin'] = 0;

    echo 'ok'; // success response
} else {
    // Server/database error
    http_response_code(500);
    echo 'error';
}
?>