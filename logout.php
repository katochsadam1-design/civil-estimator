<?php
// Start the current session
session_start();

// Destroy all session data completely
session_destroy();

// Redirect immediately to the admin login page
header('Location: admin_login.php');
exit;
?>