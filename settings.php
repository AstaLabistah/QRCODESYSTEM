<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "qrdb");
$username = $_SESSION['username'];
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current, $hash)) {
        $msg = "<div class='alert alert-danger'>Current password is incorrect!</div>";
    } elseif ($new !== $confirm) {
        $msg = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } elseif (strlen($new) < 6) {
        $msg = "<div class='alert alert-danger'>Password must be at least 6 characters.</div>";
    } else {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password=? WHERE username=?");
        $update->bind_param("ss", $new_hash, $username);
        $update->execute();
        $update->close();
        $msg = "<div class='alert alert-success' id='success-msg'>Password changed! Redirecting to login in <span id='countdown'>3</span>...</div>";
        $redirect = true;
        session_destroy();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <?php if (!empty($redirect)): ?>
    <script>
        let countdown = 3;
        function doCountdown() {
            const counter = document.getElementById('countdown');
            if (countdown > 1) {
                countdown--;
                counter.textContent = countdown;
                setTimeout(doCountdown, 1000);
            } else {
                window.location.href = "/QRCodeAttendance/loginpage/login.php";
            }
        }
        window.onload = doCountdown;
    </script>
    <?php endif; ?>
</head>
<body class="container mt-5">
    <h2 class="mb-4">Change Password</h2>
    <?php if (isset($msg)) echo $msg; ?>
    <?php if (empty($redirect)): ?>
    <form method="POST">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button class="btn btn-primary" type="submit">Update Password</button>
        <a href="index.php" class="btn btn-secondary ml-2">Back to Homepage</a>
    </form>
    <?php endif; ?>
</body>
</html>
