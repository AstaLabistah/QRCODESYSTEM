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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM student WHERE STUDENTID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: students.php");
exit();
