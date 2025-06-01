<?php
session_start();

if (!isset($_SESSION['user_db'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $attendance_id = $_POST['id'];

    $sql = "DELETE FROM attendance WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Attendance record deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete the attendance record. MySQL error: " . $stmt->error;
    }

    $stmt->close();
    header("Location: attendance.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: attendance.php");
    exit();
}
?>
