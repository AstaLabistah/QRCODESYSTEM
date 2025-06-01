<?php
session_start();

if (!isset($_SESSION['user_db'])) {
    die("No database selected. Please login.");
}

$conn = new mysqli("localhost", "root", "", $_SESSION['user_db']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
