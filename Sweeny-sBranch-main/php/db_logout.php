<?php
// Start the session so we can access and clear session data
session_start();

// Clear all session variables (empties the $_SESSION array)
$_SESSION = [];

// Unset all session variables (another way to clear session data)
session_unset();

// Destroy the session completely on the server
session_destroy();

// Send a response back to the client confirming logout
echo 'ok';
?>