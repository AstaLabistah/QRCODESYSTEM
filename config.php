<?php
require 'vendor/autoload.php';

// Replace these values with your actual credentials
define('CLIENT_ID', '191528498850-1tsfjcl8bt9kpsck979va5h54tfmf2dg.apps.googleusercontent.com');
define('CLIENT_SECRET', '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');
define('REDIRECT_URI', 'http://localhost/QRCodeAttendance/loginpage/oauth2callback.php');
define('REFRESH_TOKEN', '1//0guWJrqU-1mc4CgYIARAAGBASNwF-L9IrxZdTJd2r7bQSbJ7zoki1rMxR9egZ41WZF3WVnRofhczUBToBbLPGAy_arDMz0aNBYpo'); // Use the exact new token you got after logging in with sender Gmail
define('EMAIL_FROM', 'francismalilay@gmail.com');
define('EMAIL_NAME', 'QRCode Attendance');
?>
