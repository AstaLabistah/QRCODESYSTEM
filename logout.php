<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page (go up one directory)
header("Location: /QRCodeAttendance/loginpage/login.php");
exit();
?>