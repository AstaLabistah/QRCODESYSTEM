<?php
// changepass.php

session_start();
if (!isset($_GET['token'])) {
    // If no token, redirect to login
    header("Location: /QRCodeAttendance/loginpage/login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "qrdb");

// Get the token from URL
$token = $_GET['token'];

// Find user with this reset token
$stmt = $conn->prepare("SELECT email FROM users WHERE reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

if (!$email) {
    $msg = "<div class='alert alert-danger'>Invalid or expired password reset link.</div>";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $msg = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } elseif (strlen($new) < 6) {
        $msg = "<div class='alert alert-danger'>Password must be at least 6 characters.</div>";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);

        // Update password and clear reset token
        $update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL WHERE email=?");
        $update->bind_param("ss", $new_hash, $email);
        $update->execute();
        $update->close();

        $msg = "<div class='alert alert-success'>Password changed! Redirecting to login in <span id='countdown'>3</span>...</div>
        <script>
        let c = 3;
        setInterval(function() {
          c--;
          if (c < 1) window.location = '/QRCodeAttendance/loginpage/login.php';
          else document.getElementById('countdown').textContent = c;
        }, 1000);
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Set New Password</h2>
    <?php if (isset($msg)) echo $msg; ?>
    <?php if (empty($msg) || strpos($msg, 'alert-success') === false): ?>
    <form method="POST">
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button class="btn btn-primary" type="submit">Change Password</button>
    </form>
    <?php endif; ?>
</body>
</html>
