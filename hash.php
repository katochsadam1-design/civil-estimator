<?php
// Define the plain text password for admin
$plain_password = 'password123';

// Generate a secure bcrypt hash of the password
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Display the hashed password on the screen
echo "<h3>Your Secure Hashed Password:</h3>";
echo "<code>" . $hashed_password . "</code>";
echo "<p>Copy this hash if you want to store it securely in your database or configuration.</p>";
?>